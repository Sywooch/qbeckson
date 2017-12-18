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
    public $certificate_can_use_current_balance;

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
            ['certificate_can_use_current_balance', 'boolean'],
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
            'certificate_can_use_current_balance' => 'Доступно заключение договоров на текущий период:  от ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_from) . ' по ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_to),
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
     * если наступила дата запрета создания договора в текущем периоде ($payer->certificate_cant_use_current_balance_at),
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

        if (!$payer->canChangeContractCreatePermission()) {
            return false;
        }

        $payer->certificate_can_use_current_balance = $this->certificate_can_use_current_balance;

        if (!$payer->certificate_can_use_current_balance) {
            $payer->certificate_cant_use_current_balance_at = date('Y-m-d H:i:s', strtotime('+2 Days'));
        } else {
            $payer->certificate_cant_use_current_balance_at = null;
        }

        if (!$payer->save()) {
            $payer->certificate_can_use_current_balance = $payer->getOldAttribute('certificate_can_use_current_balance');

            return false;
        }

        $certificateUserIds = ArrayHelper::getColumn($payer->getCertificates()->distinct()->select('user_id')->asArray()->all(), 'user_id');
        $organizationUserIds = ArrayHelper::getColumn($payer->getOrganizations()->select('user_id')->asArray()->all(), 'user_id');

        $messageForCertificates = 'C ' . \Yii::$app->formatter->asDate(date('Y-m-d', strtotime($payer->certificate_cant_use_current_balance_at))) . ' установлено ограничение на заключение новых договоров, с указанного числа Вы не сможете подать новые заявки на обучение на период с ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_from) . ' по ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_to) . '.';
        $messageForOrganizations = 'Уполномоченной организацией ' . $payer->name . ' установлено ограничение на заключение новых договоров с детьми с ' . \Yii::$app->formatter->asDate(date('Y-m-d', strtotime($payer->certificate_cant_use_current_balance_at))) . '. Формирование новых заявок по сертификатам данной уполномоченной организации на обучение будет недоступно с указанной даты.';

        $notificationForCertificates = Notification::getExistOrCreate($messageForCertificates, 0, Notification::TYPE_CERTIFICATE_CANT_USE_CURRENT_BALANCE);
        $notificationForOrganizations = Notification::getExistOrCreate($messageForOrganizations, 0, Notification::TYPE_CERTIFICATE_CANT_USE_CURRENT_BALANCE);

        // уведомления сертификатов
        if ($notificationForCertificates) {
            if ($payer->certificate_can_use_current_balance == 0) {
                NotificationUser::assignToUsers($certificateUserIds, $notificationForCertificates->id);
            } else {
                $notificationForCertificates->delete();
            }
        }

        // уведомление организаций
        if ($notificationForOrganizations) {
            if ($payer->certificate_can_use_current_balance == 0) {
                NotificationUser::assignToUsers($organizationUserIds, $notificationForOrganizations->id);
            } else {
                $notificationForOrganizations->delete();
            }
        }

        return true;
    }
}