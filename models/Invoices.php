<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoices".
 *
 * @property integer $id
 * @property integer $month
 * @property integer $organization_id
 * @property integer $payers_id
 * @property integer $contract_id
 * @property integer $sum
 * @property integer $number
 * @property string $date
 * @property string $link
 * @property integer $prepayment
 *
 * @property Contracts $contract
 * @property Organization $organization
 * @property Payers $payers
 */
class Invoices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['month', 'organization_id', 'payers_id', 'completeness', 'number', 'prepayment', 'status'], 'integer'],
            [['organization_id', 'payers_id', 'contracts', 'status'], 'required'],
            [['date'], 'safe'],
            [['sum'], 'number'],
            [['link', 'contracts'], 'string'],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
            [['payers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payers::className(), 'targetAttribute' => ['payers_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'month' => 'Месяц, за который выставлен счет ',
            'organization_id' => 'Organization ID',
            'payers_id' => 'Payers ID',
            'contracts' => 'Контракты',
            'sum' => 'Сумма счета',
            'number' => 'Номер счета',
            'date' => 'Дата счета',
            'link' => 'Ссылка на документ ',
            'prepayment' => 'Аванс',
            'completeness' => 'ID полноты оказаных услуг',
            'status' => 'Статус',
        ];
    }
    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'organization_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayers()
    {
        return $this->hasOne(Payers::className(), ['id' => 'payers_id']);
    }
    
    
}
