<?php

namespace app\models\invoices;


use app\components\SingleModelActions;
use app\models\Invoices;
use yii\helpers\ArrayHelper;

/**
 * Class InvoicesActions
 * @package app\models\invoices
 *
 * @property Invoices $invoice
 *
 */
abstract class InvoicesActions extends SingleModelActions
{
    /** класс модели над которой производятся действия */
    public static function getTargetModelClass(): string
    {
        return Invoices::className();
    }

    /**
     * @return \yii\db\ActiveRecord
     */
    public function getInvoice()
    {
        return $this->targetModel;
    }


    /**
     * @param $invoice
     */
    public function setInvoice($invoice)
    {
        parent::setTargetModel($invoice);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'invoice' => 'Счет',
        ]);
    }
}