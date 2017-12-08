<?php

namespace app\models;

use yii\db\Expression;

/**
 * This is the model class for table "completeness".
 *
 * Платеж по договору в конкретном месяце
 *
 * В каждом договоре прописывается платеж за первый и прочие месяцы, на каждый месяц идет два  платежа (авансовый комплитнесс "в
 * сентябре 80% денег за сентябрь" и счетовой комплитнесс "в октябре 100% за сентябрь").
 * Комплитнессы создаются когда договор переводится в действующие (в статус 1) и при  наступлении нового месяца (крон)
 *
 * @property integer $id
 * @property integer $group_id
 * @property integer $contract_id
 * @property integer $month
 * @property integer $year
 * @property integer $completeness
 * @property float $sum
 * @property integer $preinvoice   0 - окончательный расчет,  1 - авансовый
 *
 * @property \DateTime $date
 * @property string $preinvoiceLabel
 *
 *
 *
 * @property Groups $group
 */
class Completeness extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%completeness}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['month', 'year', 'completeness'], 'required'],
            [['group_id', 'contract_id', 'month', 'year', 'preinvoice'], 'integer'],
            [['completeness'], 'integer', 'max' => 100],
            [['sum'], 'number'],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Groups::className(), 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    public function getDate()
    {
        return (new \DateTime())->setDate($this->year, $this->month, 1);
    }

    public function getPreinvoiceLabel()
    {
        if ($this->preinvoice) {
            return 'Аванс';
        } else {
            return 'Счет';
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group_id' => 'ID Группы',
            'month' => 'Месяц',
            'year' => 'Год',
            'completeness' => 'Полнота услуг оказанных организацией',
            'preinvoice' => 'Предоплата',
            'sum' => 'Сумма',
            'preinvoiceLabel' => 'Тип счета',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Groups::className(), ['id' => 'group_id']);
    }

    public function getIsPaid(): bool
    {
        $date = new \DateTime($this->date);
        $beginMoth = $date->modify('first day of this month')->format('Y-m-d');
        $endMoth = $date->modify('first day of this month')->format('Y-m-d');
        $contractCollectionExpression = <<<SQL

SQL;
        $condition = [
            'and',
            ['between', 'date', $beginMoth, $endMoth],
            ['in', $this->contract_id, new Expression($contractCollectionExpression)]
        ];

        return Invoices::find()->where($condition)->exists();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Contracts::className(), ['id' => 'contract_id']);
    }

    public static function findPreinvoiceByContract($contractId, $month = null, $year = null)
    {
        $query = static::find()
            ->where([
                'preinvoice' => 1,
                'contract_id' => $contractId,
            ]);

        $query->andFilterWhere([
            'month' => $month,
            'year' => $year,
        ]);

        return $query->one();
    }

    public static function findInvoicesByContracts($contractIds, $month = null, $year = null)
    {
        $query = static::find()
            ->where([
                'preinvoice' => 0,
                'contract_id' => $contractIds,
            ])
            ->andWhere(['<', 'completeness', 100]);

        $query->andFilterWhere([
            'month' => $month,
            'year' => $year,
        ]);

        return $query->all();
    }
}
