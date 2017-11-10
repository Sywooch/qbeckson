<?php

namespace app\models\certificates;

use app\models\Model;

/**
 * Форма подтверждения изменения типа сертификата в сертификат учета
 */
class CertificateToAccountingConfirmForm extends Model
{
    /**
     * подтверждена ли смена типа
     *
     * @var boolean
     */
    public $changeTypeConfirmed;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['changeTypeConfirmed', 'required', 'requiredValue' => 1, 'message' => 'Для перевода сертификатов в сертификаты учета вам необходимо подтвердить процедуру.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'changeTypeConfirmed' => 'Подтверждаем, что хотим применить данную процедуру',
        ];
    }
}