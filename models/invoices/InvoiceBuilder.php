<?php

namespace app\models\invoices;


use app\helpers\DeclinationOfMonths;
use app\models\Completeness;
use app\models\Contracts;
use app\models\InvoiceHaveContract;
use app\models\Invoices;
use app\models\Organization;
use app\models\Payers;
use app\models\UserIdentity;
use DateTime;
use Yii;
use yii\base\Event;
use yii\base\InvalidParamException;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\validators\InlineValidator;

/**
 * Class InvoiceBuilder
 * @package app\models\invoices
 *
 * @property string $date
 * @property integer $number
 *
 * @property array $contractsData
 * @property integer $datePrevMonthNumber
 * @property DateTime $datePrevMonthBeginning
 * @property DateTime $datePrevMonthEnd
 * @property Contracts[] $outOfRangeContracts
 * @property boolean $haveOutOfRangeContracts
 *
 */
class InvoiceBuilder extends InvoicesActions
{

    const MSG_REFUZE_NEW_CONTRACT = 'Истек срок рассмотрения заявки со стороны организации, пожалуйста, сформируйте новую.';
    const MSG_REFUZE_ACCEPTED_CONTRACT = 'Оферта отозвана исполнителем в связи с истечением срока ожидания акцепта.';
    public $date;
    public $number;
    public $payer_id;
    public $isDecember = false;
    /**
     * @var Organization
     */
    public $organization;
    /**
     * @var Contracts[]
     */
    private $_outOfRangeContracts;

    private $_contractsData;

    /**
     * @param $params array|null
     *
     * @return InvoiceBuilder
     */
    public static function createInstance($params = []): self
    {
        if (array_key_exists('payer_id', $params)) {
            $payer_id = $params['payer_id'];
        } else {

            throw new InvalidParamException('payer_id required');
        }
        $date = $params['date'] ?? date("Y-m-d");
        $organization = $params['organization'] ?? Yii::$app->user->identity->organization;

        return new self([
            'invoice' => new Invoices(),
            'date' => $date,
            'organization' => $organization,
            'payer_id' => $payer_id,
            'isDecember' => (isset($params['isDecember']) && $params['isDecember'] === true) ? true : false,
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'date' => 'Дата счета',
            'number' => 'Номер счета'
        ]);
    }


    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['number', 'date'], 'required'],
            ['number', 'integer'],
            ['date', 'safe'],
            ['date', 'invoiceExistsValidator'],
            ['date', 'contractsValidator'],
            ['invoice', 'contractUniqueValidator']
        ]);
    }


    public function emitDuplicatedContractIdErrsDangerFlash($contractIds)
    {
        $message = \yii\widgets\ListView::widget([
            'dataProvider' => new ArrayDataProvider(['allModels' => $contractIds]),
            'summary' => 'ОШИБКА! Сделайте скриншот данной страницы и отправьте в тех-поддержку : {totalCount}',
            'options' => ['tag' => 'ul', 'class' => 'list-unstyled'],
            'itemOptions' => ['tag' => 'li'],
            'itemView' => function ($value, $key, $index, $widget) {
                $link = Html::a('Договор: ' . $value, ['contracts/view', 'id' => $value]);

                return $link;
            }
        ]);
        Yii::$app->session->addFlash('danger', $message);
    }

    public function contractUniqueValidator($attribute, $params, InlineValidator $validator)
    {
        $contractsQuery = Contracts::find()
            ->innerJoin(
                Completeness::tableName(),
                ['contract_id' => new Expression(Contracts::tableName() . '.[[id]]')]
            );
        $duplicatedContractId = $this->applyContractsCondition($contractsQuery)
            ->having('count(' . Contracts::tableName() . '.[[id]]) > 1')
            ->groupBy(Contracts::tableName() . '.[[id]]')
            ->select(['id' => 'max(' . Contracts::tableName() . '.[[id]])'])
            ->column();

        // добавить ошибку только если есть дубликаты
        if ($duplicatedContractId) {
            $this->addError($attribute, 'Дубликаты договоров: ' . implode(', ', $duplicatedContractId));
            $this->emitDuplicatedContractIdErrsDangerFlash($duplicatedContractId);
        }
    }

    public function contractsValidator($attribute, $params, InlineValidator $validator)
    {
        if (mb_strlen($this->contractsData['contracts']) < 1) {
            $this->addError($attribute, 'Не обнаружено не одного договора');
        }
    }

    /**Существует в этом месяце, этом году, для этого плательщика у этой организации, и статус не "удален"*/
    public function invoiceExistsValidator($attribute, $params, InlineValidator $validator)
    {
        if (Invoices::find()
            ->where([
                'payers_id' => $this->payer_id,
                'organization_id' => $this->organization->id,
                'month' => $this->datePrevMonthNumber,
                'prepayment' => 0,
            ])
            ->andWhere([
                '<=', 'date', (new \DateTime($this->date))->format('Y') . '-12-31'
            ])
            ->andWhere([
                '>=', 'date', (new \DateTime($this->date))->format('Y') . '-01-01'
            ])
            ->andWhere(['!=', 'status', Invoices::STATUS_REMOVED])
            ->exists()) {

            $msg = 'За {mothDateStr} в {year} уже существует счет для {payer}';
            $msgParams = [
                'mothDateStr' => DeclinationOfMonths::getMonthNameByNumberAsNominative($this->datePrevMonthNumber),
                'year' => (new \DateTime($this->date))->format('Y'),
                'payer' => Payers::findOne($this->payer_id)->name
            ];
            $this->addError($attribute, Yii::t('app', $msg, $msgParams));
        }

    }

    public function getOutOfRangeContracts()
    {
        if (is_null($this->_outOfRangeContracts)) {
            $this->buildOutOfRangeContracts();
        }

        return $this->_outOfRangeContracts;
    }

    private function buildOutOfRangeContracts()
    {
        $this->_outOfRangeContracts = $this->organization
            ->getContracts()
            ->andWhere([
                Contracts::tableName() . '.status' => [
                    Contracts::STATUS_REQUESTED,
                    Contracts::STATUS_ACCEPTED
                ]
            ])
            ->andWhere([Contracts::tableName() . '.payer_id' => $this->payer_id])
            ->andWhere(['<=', 'start_edu_contract', $this->datePrevMonthEnd])
            ->all();
    }

    public function setAfterSaveInvoiceHaveContractsCreateAction(\Closure $transactionTerminator)
    {
        $contractIds =  array_unique($this->getContractsIds());

        $action = function (Event $event) use ($transactionTerminator, $contractIds) {
            /**@var $invoice Invoices */
            $invoice = $event->sender;
            foreach ($contractIds as $contractId) {
                $linker = new InvoiceHaveContract(
                    ['invoice_id' => $invoice->id, 'contract_id' => $contractId]
                );
                if (!$linker->save()) {
                    $this->addError('invoice', 'Не удалось привязать договора');

                    return $transactionTerminator();
                }
            }

            return true;
        };

        $this->invoice->on(ActiveRecord::EVENT_AFTER_INSERT, $action);
        $this->invoice->on(ActiveRecord::EVENT_AFTER_UPDATE, $action);
    }

    public function getHaveOutOfRangeContracts(): bool
    {
        if (is_null($this->_outOfRangeContracts)) {
            $this->buildOutOfRangeContracts();
        }

        return count($this->outOfRangeContracts) > 0;
    }

    public function getLmonth()
    {
        return Yii::$app->params['decemberNumber'];
    }

    public function getDatePrevMonthEnd(): string
    {
        if ($this->isDecember) {
            $cal_days_in_month = cal_days_in_month(CAL_GREGORIAN, $this->lmonth, date('Y'));

            return date('Y') . '-' . $this->lmonth . '-' . $cal_days_in_month;
        }

        $date = new DateTime();
        $date->modify('last day of previous month');

        return $date->format('Y-m-d');
    }

    public function getDatePrevMonthBeginning(): string
    {
        if ($this->isDecember) {
            return date('Y') . '-' . $this->lmonth . '-' . '01';
        }

        $date = new DateTime();
        $date->modify('first day of previous month');

        return $date->format('Y-m-d');
    }

    public function getDatePrevMonthNumber(): int
    {
        if ($this->isDecember) {
            return $this->lmonth;
        }

        $date = new DateTime();
        $date->modify('first day of previous month');

        return (int)$date->format('m');
    }

    public function getDatePrevYearNumber(): int
    {
        $date = new DateTime();
        $date->modify('first day of previous month');

        return (int)$date->format('Y');
    }

    /**
     * Все манипуляции внутри этой функции происходят в трансзакции, можно прервать трансзакцию из нутри.
     * для успешного завершения вернуть true
     *
     * @param \Closure $transactionTerminator
     * @param bool $validate
     *
     * @return bool
     */
    public function saveActions(\Closure $transactionTerminator, bool $validate): bool
    {
        $this->setAfterSaveInvoiceHaveContractsCreateAction($transactionTerminator);
        $this->invoice->setAttributes($this->getContractsData()); /*заполняем данные договоров*/
        $this->fillFieldsOfInvoice();             /* заполняем скалярные данные*/
        $this->invoice->setCooperate();

        if (!$this->refuseNotActiveContracts()) {
            return $transactionTerminator();
        }

        $this->invoice->pdf = $this->invoice->generateInvoice();  /* Генерируем файл отчета */

        return true;
    }

    private function applyContractsCondition(ActiveQuery $contractQuery): ActiveQuery
    {
        $contractQuery
            ->andWhere(['preinvoice' => 0])
            ->andWhere(['month' => $this->datePrevMonthNumber])
            ->andWhere(['<=', 'start_edu_contract', $this->datePrevMonthEnd])
            ->andWhere(['>=', 'stop_edu_contract', $this->datePrevMonthBeginning])
            /* если договор закрыт, учитываем так же дату закрытия, а если активен то НЕ учитываем */
//            ->andWhere(
//                [
//                    'OR',
//                    [
//                        'AND',
//                        ['<=', 'date_termnate', $this->datePrevMonthEnd],
//                        ['`contracts`.status' => Contracts::STATUS_CLOSED]
//                    ],
//                    [
//                        '`contracts`.status' => Contracts::STATUS_ACTIVE
//                    ]
//                ]
//            )
            ->andWhere(
                [
                    'OR',
                    [
                        'AND',
                        ['>=', 'date_termnate', $this->datePrevMonthBeginning],
                        ['`contracts`.status' => Contracts::STATUS_CLOSED]
                    ],
                    [
                        '`contracts`.status' => Contracts::STATUS_ACTIVE
                    ]
                ]
            )
            ->andWhere([Contracts::tableName() . '.organization_id' => $this->organization->id])
            ->andWhere([Contracts::tableName() . '.payer_id' => $this->payer_id])
            ->andWhere(['>', 'all_funds', 0]);

        return $contractQuery;
    }

    public function getContractsData()
    {
        if (!$this->_contractsData) {
            $contractsQuery = Contracts::find()
                ->select([
                    'contracts' => new Expression('GROUP_CONCAT(' . Contracts::tableName() . '.{{id}})'),
                    'sum' => new Expression('ROUND(SUM(' . Completeness::tableName() . '.{{sum}}), 2)')
                ])
                ->innerJoin(
                    Completeness::tableName(),
                    ['contract_id' => new Expression(Contracts::tableName() . '.{{id}}')]
                );
            $this->_contractsData = $this->applyContractsCondition($contractsQuery)
                ->asArray()
                ->one();
        }

        return $this->_contractsData;
    }

    public function getContractsIds(): array
    {
        $contractsQuery = Contracts::find()
            ->innerJoin(
                Completeness::tableName(),
                ['contract_id' => new Expression(Contracts::tableName() . '.{{id}}')]
            );

        return $this->applyContractsCondition($contractsQuery)->column();
    }

    private function fillFieldsOfInvoice()
    {
        $this->invoice->date = $this->date;
        $this->invoice->number = $this->number;
        $this->invoice->month = $this->datePrevMonthNumber;
        $this->invoice->year = $this->datePrevYearNumber;
        $this->invoice->prepayment = 0;
        $this->invoice->status = Invoices::STATUS_NOT_VIEWED;
        $this->invoice->organization_id = $this->organization->id;
        $this->invoice->payers_id = $this->payer_id;
    }

    private function refuseNotActiveContracts()
    {
        if ($this->haveOutOfRangeContracts) {
            foreach ($this->outOfRangeContracts AS $contract) {
                $message = $contract->status === Contracts::STATUS_ACCEPTED
                    ? self::MSG_REFUZE_ACCEPTED_CONTRACT
                    : self::MSG_REFUZE_NEW_CONTRACT;
                if (!$contract->setRefused($message, UserIdentity::ROLE_ORGANIZATION_ID, $contract->organization_id)) {
                    $this->addError('outOfRangeContracts', 'Не удалось расторгнуть договор ' . $contract->id);

                    return false;
                }
            }
        }

        return true;
    }
}
