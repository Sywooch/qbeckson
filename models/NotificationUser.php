<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * назначение уведомлений пользователям
 *
 * @property int $id
 * @property int $user_id
 * @property int $notification_id
 */
class NotificationUser extends ActiveRecord
{
    /**
     * назначить уведомление пользователям
     *
     * @param $userIdList - список id пользователей
     * @param $notificationId - id уведомления
     */
    public static function assignToUsers($userIdList, $notificationId)
    {
        $notificationUserList = ArrayHelper::getColumn(self::find()->select(['user_id'])->where(['notification_id' => $notificationId])->asArray()->all(), 'user_id');

        $rows = [];
        foreach ($userIdList as $userId) {
            if (!in_array($userId, $notificationUserList)) {
                $rows[] = [$userId, $notificationId];
            }
        }

        \Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['user_id', 'notification_id'], $rows)->execute();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification_user';
    }

    /**
     * удалить уведомление для текущего пользователя
     *
     * @param $notificationId - id уведомления
     *
     * @return bool
     */
    public static function deleteForCurrentUser($notificationId)
    {
        if (\Yii::$app->user->isGuest) {
            return false;
        }

        $notificationUser = NotificationUser::findOne(['user_id' => \Yii::$app->user->identity->id, 'notification_id' => $notificationId]);

        if (!$notificationUser || !$notificationUser->delete()) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notification_id', 'user_id'], 'required'],
            ['notification_id', 'exist', 'targetClass' => Notification::className(), 'targetAttribute' => 'id'],
            ['user_id', 'exist', 'targetClass' => User::className(), 'targetAttribute' => 'id'],
            [['user_id', 'notification_id'], 'unique', 'targetAttribute' => ['user_id', 'notification_id']],
        ];
    }
}
