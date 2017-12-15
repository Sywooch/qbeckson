<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * уведомление пользователя
 *
 * @property int $id
 * @property int $user_id идентификатор пользователя
 * @property string $message текст уведомления
 * @property bool $delete_after_show удалять ли уведомление после отображения
 * @property string $type тип уведомления
 *
 * @property ActiveQuery $users
 */
class Notification extends ActiveRecord
{
    /** уведомление сертификатов о запрете на создание контрактов текущего периода */
    const TYPE_CERTIFICATE_CANT_USE_CURRENT_BALANCE = 'certificate_cant_use_current_balance';

    /** уведомление организаций (имеющих соглашение на будущий период) о запрете на создание контрактов будущего периода */
    const TYPE_CERTIFICATE_WITH_FUTURE_COOPERATE_CAN_USE_FUTURE_BALANCE = 'certificate_with_future_cooperate_can_use_future_balance';

    /** уведомление организаций (не имеющих соглашение на будущий период) о запрете на создание контрактов будущего периода */
    const TYPE_CERTIFICATE_WITHOUT_FUTURE_COOPERATE_CAN_USE_FUTURE_BALANCE = 'certificate_without_future_cooperate_can_use_future_balance';

    /** уведомление организаций (имеющие действующее соглашение) о приостановлении возможности заключения договоров в будущем периоде */
    const TYPE_CERTIFICATE_CANT_USE_FUTURE_BALANCE = 'certificate_cant_use_future_balance';

    /** уведомление о переводе сертификатов в "сертификат учета" */
    const TYPE_CERTIFICATE_TO_ACCOUNTING = 'certificate_to_accounting_type';

    /** уведомление об отклонении заявки на изменение данных муниципалитета */
    const TYPE_MUN_APPLICATION_REJECT = 'mun_application_reject';

    /** @inheritdoc */
    public static function tableName()
    {
        return 'notification';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            ['message', 'required'],
            [['message', 'type'], 'string'],
            ['delete_after_show', 'boolean'],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'message' => 'текст уведомления',
            'delete_after_show' => 'удалять ли уведомление после первого отображения',
            'type' => 'тип уведомления',
        ];
    }

    /**
     * получить уведомление пользователя с указанным типом, которое есть в БД, если его нет, то оно создается
     *
     * @param $message - текст уведомления
     * @param int $delete_after_show - удалять ли уведомление после первого отображения
     * @param string $type - тип уведомления
     *
     * @return Notification|null
     */
    public static function getExistOrCreate($message, $delete_after_show = 1, $type = null)
    {
        if ($existNotification = self::findOne(['user_id' => \Yii::$app->user->identity->id, 'type' => $type])) {
            $existNotification->message = $message;
            $existNotification->delete_after_show = $delete_after_show;

            if (!$existNotification->save()) {
                return null;
            }

            return $existNotification;
        }

        $newNotification = new self(['message' => $message, 'delete_after_show' => $delete_after_show, 'user_id' => \Yii::$app->user->identity->id, 'type' => $type]);
        if (!$newNotification->save()) {
            return null;
        }

        return $newNotification;
    }
}