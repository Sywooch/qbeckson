<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 16.10.2017
 * Time: 11:38
 */

namespace app\models\invoices;


use app\models\Contracts;
use app\models\Invoices;
use app\models\Organization;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class InvoiceBuilder
 * @package app\models\invoices
 *
 * @property string $date
 * @property integer $number
 *
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
    /**
     * @var Organization
     */
    public $organization;
    /**
     * @var Contracts[]
     */
    private $_outOfRangeContracts;

    /**
     * @param $params array|null
     *
     * @return InvoiceBuilder
     */
    public static function createInstance($params = []): self
    {
        $date = $params['date'] ?? date("Y-m-d");
        $organization = $params['organization'] ?? Yii::$app->user->identity->organization;

        return new self([
            'invoice' => new Invoices(),
            'date' => $date,
            'organization' => $organization
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
        ]);
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
        $lastDate = new \DateTime($this->date);
        $lastDate->modify('last day of');
        $this->_outOfRangeContracts = $this->organization
            ->getContracts()
            ->andWhere([
                Contracts::tableName() . '.status' => [
                    Contracts::STATUS_CREATED,
                    Contracts::STATUS_ACCEPTED
                ]
            ])
            ->andWhere(['<=', 'start_edu_contract', $lastDate->format('Y-m-d')])
            ->all();
    }

    public function gethaveOutOfRangeContracts(): bool
    {
        if (is_null($this->_outOfRangeContracts)) {
            $this->buildOutOfRangeContracts();
        }

        return count($this->outOfRangeContracts) > 0;
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
        // TODO: Implement saveActions() method.
    }

    private function getAndCalcContractsData()
    {

    }


}