<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\Organization */

$updateLink = Yii::$app->urlManager->createAbsoluteUrl(['organization/request-update', 'token' => $model->anonymous_update_token]);
?>
<div class="password-reset">
    <p>Здравствуйте!</p>

    <p>Ваша заявка успешно отправлена на проверку оператору системы.</p>

    <p>Вы можете посмотреть статус заявки по ссылке <?= Html::a(Html::encode($updateLink), $updateLink) ?></p>
</div>
