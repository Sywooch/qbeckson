<?php

namespace app\controllers;

use app\models\NotificationUser;
use yii\web\Controller;

/**
 * Class NotificationController
 */
class NotificationController extends Controller
{
    /**
     * удалить уведомление пользователя
     *
     * @param $notificationId - id уведомления
     *
     * @return \yii\web\Response
     */
    public function actionDelete($notificationId)
    {
        $deleted = NotificationUser::deleteForCurrentUser($notificationId);

        return $this->asJson($deleted);
    }
}
