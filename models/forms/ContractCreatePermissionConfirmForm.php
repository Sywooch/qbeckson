<?php

namespace app\models\forms;

use app\models\Model;
use app\models\Notification;
use app\models\NotificationUser;
use app\models\Payers;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * форма подтверждения запрета на создание контрактов сертификатами
 */
class ContractCreatePermissionConfirmForm extends Model
{
    /**
     * пароль для подтверждения запрета
     *
     * @var string
     */
    public $password;

    /**
     * разрешено ли сертификату создавать контракт
     *
     * @var boolean
     */
    public $certificate_can_create_contract;

    /**
     * подтверждение изменения разрешения на создание договоров
     *
     * @var boolean
     */
    public $changePermissionConfirm;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required', 'on' => 'deny_to_create_contract'],
            ['certificate_can_create_contract', 'boolean'],
            ['changePermissionConfirm', 'compare', 'compareValue' => 1, 'operator' => '==', 'message' => 'необходимо подтвердить запрет на заключение новых договоров', 'except' => 'allow_to_create_contract'],
            ['password', 'passwordValidator'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => 'Пароль',
            'certificate_can_create_contract' => 'Доступно заключение договоров на текущий период:  от ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_from) . ' по ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_to),
            'changePermissionConfirm' => 'Да, мы уверены',
        ];
    }

    /**
     * валидация пароля пользователя
     *
     * @param $attribute
     *
     * @return boolean
     */
    public function passwordValidator($attribute)
    {
        if (YII_ENV_DEV) {
            return true;
        }

        if (!Yii::$app->user->identity->validatePassword($this->password)) {
            $this->addError($attribute, 'Неверно указан пароль');

            return false;
        }

        return true;
    }

    /**
     * изменить разрешение на создание договоров
     * ---
     * если наступила дата запрещения на создание договора ($payer->certificate_cant_create_contract_at),
     * то изменить разрешение уже нельзя
     *
     * @param Payers $payer - плательщик
     *
     * @return bool
     */
    public function changeContractCreatePermission($payer)
    {
        if (!$this->validate()) {
            return false;
        }

        if (!$payer->canChangePermission()) {
            return false;
        }

        $payer->certificate_can_create_contract = $this->certificate_can_create_contract;

        if (!$payer->certificate_can_create_contract) {
            $payer->certificate_cant_create_contract_at = date('Y-m-d H:i:s', strtotime('+2 Days'));
        } else {
            $payer->certificate_cant_create_contract_at = null;
        }

        if (!$payer->save()) {
            $payer->certificate_can_create_contract = $payer->getOldAttribute('certificate_can_create_contract');

            return false;
        }

        return true;
    }
}