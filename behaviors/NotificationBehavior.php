<?php

namespace app\behaviors;

use app\models\Notification;
use app\models\NotificationUser;
use yii\base\Behavior;
use yii\helpers\Url;
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

        /** @var Notification[] $notifications */
        $notifications = \Yii::$app->user->identity->notifications;

        if ($notifications) {
            foreach ($notifications as $notification) {
                $deleteLink = !$notification->delete_after_show ? '<a href="javascript:void(0)" onclick="$.ajax(\'' . Url::to(['/notification/delete', 'notificationId' => $notification->id]) . '\'); $(this).parent().remove()"> Удалить уведомление</a>' : '';
                \Yii::$app->session->addFlash('warning', $notification->message . $deleteLink, false);

                if ($notification->delete_after_show) {
                    NotificationUser::deleteForCurrentUser($notification->id);
                }
            }
        }
    }
}