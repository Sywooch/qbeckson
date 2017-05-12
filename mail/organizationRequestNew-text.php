<?php

/* @var $this yii\web\View */
/* @var $user app\models\Organization */

$updateLink = Yii::$app->urlManager->createAbsoluteUrl(['organization/request-update', 'token' => $model->anonymous_update_token]);
?>
Здравствуйте!

Ваша заявка успешно отправлена на проверку оператору системы.

Вы можете посмотреть статус заявки по ссылке <?= $updateLink ?></p>
