<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\Organization */

$updateLink = Yii::$app->urlManager->createAbsoluteUrl(['organization/request-update', 'token' => $model->anonymous_update_token]);
?>
Здравствуйте!

Ваша заявка отклонена.

Вы можете отредактировать заявку и повторно отправить на модерацию по ссылке <?= $updateLink ?>
