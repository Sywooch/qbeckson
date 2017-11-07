<?php

namespace app\models\invoices;


use app\helpers\DeclinationOfMonths;
use app\models\Completeness;
use app\models\Contracts;
use app\models\Invoices;
use app\models\Organization;
use app\models\Payers;
use DateTime;
use Yii;
use yii\base\InvalidParamException;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\validators\InlineValidator;

/**
 * Class InvoiceBuilder
 * @package app\models\invoices
 *
 * @property string $date
 * @property integer $number
 *
 * @property array $contractsData
 * @property integer $dateCurrentMonthNumber
 * @property DateTime $dateCurrentMonthBeginning
 * @property DateTime $dateCurrentMonthEnd
 *
 */
class PreInvoiceBuilder extends InvoicesActions
{

    public $date;
    public $number;
    public $payer_id;
    /**
     * @var Organization
     */
    public $organization;
    /**
     * @var Contracts[]
     */

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
            'payer_id' => $payer_id
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
            ['date', 'preInvoiceExistsValidator'],
            ['date', 'contractsValidator']
        ]);
    }


    public function contractsValidator($attribute, $params, InlineValidator $validator)
    {
        if (mb_strlen($this->contractsData['contracts']) < 1) {
            $this->addError($attribute, 'Не обнаружено не одного договора');
        }
    }

    /**Существует в этом месяце, этом году, для этого плательщика у этой организации, и статус не "удален"*/
    public function preInvoiceExistsValidator($attribute, $params, InlineValidator $validator)
    {
        if (Invoices::find()
            ->where([
                'payers_id' => $this->payer_id,
                'organization_id' => $this->organization->id,
                'month' => $this->dateCurrentMonthNumber,
                'prepayment' => 1,
            ])
            ->andWhere([
                '<=', 'date', (new \DateTime($this->date))->format('Y') . '-12-31'
            ])
            ->andWhere([
                '>=', 'date', (new \DateTime($this->date))->format('Y') . '-01-01'
            ])
            ->andWhere(['!=', 'status', Invoices::STATUS_REMOVED])
            ->exists()) {

            $msg = 'За {mothDateStr} в {year} уже существует предоплата для {payer}';
            $msgParams = [
                'mothDateStr' => DeclinationOfMonths::getMonthNameByNumberAsNominative($this->dateCurrentMonthNumber),
                'year' => (new \DateTime($this->date))->format('Y'),
                'payer' => Payers::findOne($this->payer_id)->name
            ];
            $this->addError($attribute, Yii::t('app', $msg, $msgParams));
        }

    }

    public function getDateCurrentMonthEnd(): string
    {
        $date = new DateTime();
        $date->modify('last day of this month');

        return $date->format('Y-m-d');
    }

    public function getDateCurrentMonthBeginning(): string
    {
        $date = new DateTime();
        $date->modify('first day of previous month');

        return $date->format('Y-m-d');
    }

    public function getDateCurrentMonthNumber(): int
    {
        $date = new DateTime();

        return (int)$date->format('m');
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
        $this->invoice->setAttributes($this->getContractsData()); /*заполняем данные договоров*/
        $this->fillFieldsOfInvoice();             /* заполняем скалярные данные*/
        $this->invoice->setCooperate();
        $this->invoice->pdf = $this->invoice->generatePrepaid();  /* Генерируем файл отчета */

        return true;

    }

    public function getContractsData()
    {
        if (!$this->_contractsData) {
            $this->_contractsData = Contracts::find()
                ->select([
                    'contracts' => new Expression('GROUP_CONCAT(' . Contracts::tableName() . '.{{id}})'),
                    'sum' => new Expression('ROUND(SUM(' . Completeness::tableName() . '.{{sum}}), 2)')
                ])
                ->innerJoin(Completeness::tableName(), ['contract_id' => new Expression(Contracts::tableName() . '.{{id}}')])
                ->andWhere([Completeness::tableName() . '.preinvoice' => 1])
                ->andWhere([Completeness::tableName() . '.month' => $this->dateCurrentMonthNumber])
                ->andWhere(['<=', 'start_edu_contract', $this->dateCurrentMonthEnd])
                ->andWhere([Contracts::tableName() . '.organization_id' => $this->organization->id])
                ->andWhere([Contracts::tableName() . '.payer_id' => $this->payer_id])
                ->andWhere([Contracts::tableName() . '.status' => Contracts::STATUS_ACTIVE])
                ->andWhere(['>', 'all_funds', 0])
                ->asArray()
                ->one();
        }

        return $this->_contractsData;

    }

    private function fillFieldsOfInvoice()
    {
        $this->invoice->date = $this->date;
        $this->invoice->number = $this->number;
        $this->invoice->month = $this->dateCurrentMonthNumber;
        $this->invoice->prepayment = 1;
        $this->invoice->status = Invoices::STATUS_NOT_VIEWED;
        $this->invoice->organization_id = $this->organization->id;
        $this->invoice->payers_id = $this->payer_id;
    }
}
