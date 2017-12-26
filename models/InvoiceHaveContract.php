<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%invoice_have_contract}}".
 *
 * @property integer $invoice_id
 * @property integer $contract_id
 *
 * @property Contracts $contract
 * @property Invoices $invoice
 */
class InvoiceHaveContract extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%invoice_have_contract}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_id', 'contract_id'], 'required'],
            [['invoice_id', 'contract_id'], 'integer'],
            [
                ['contract_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Contracts::className(), 'targetAttribute' => ['contract_id' => 'id']
            ],
            [
                ['invoice_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Invoices::className(), 'targetAttribute' => ['invoice_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'invoice_id' => 'Invoice ID',
            'contract_id' => 'Contract ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Contracts::className(), ['id' => 'contract_id'])
            ->inverseOf('invoiceHaveContracts');
    }

    public function getInvoiceMonth()
    {
        return $this->invoice->month;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoices::className(), ['id' => 'invoice_id'])
            ->inverseOf('invoiceHaveContracts');
    }
}
