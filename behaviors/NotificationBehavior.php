<?php

namespace app\behaviors;

use app\models\Notification;
use yii\base\Behavior;
use yii\web\Controller;

/**
 * поведение для уведомлений пользователей
 */
class NotificationBehavior extends Behavior
{
    /** @inheritdoc */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'addNotification',
        ];
    }

    /**
     * добавить уведомление для авторизованного пользователя, если уведомление существует
     */
    public function addNotification()
    {
        if (\Yii::$app->user->isGuest) {
            return;
        }

        $notifications = Notification::findAll(['user_id' => \Yii::$app->user->identity->id]);

        if ($notifications) {
            foreach ($notifications as $notification) {
                \Yii::$app->session->addFlash('warning', $notification->message, false);
                $notification->delete();
            }
        }
    }
}