<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\Organization */

$updateLink = Yii::$app->urlManager->createAbsoluteUrl(['organization/request-update', 'token' => $model->anonymous_update_token]);
?>
<div class="password-reset">
    <p>Здравствуйте!</p>

    <p>К сожалению Ваша заявка на включение поставщика образовательных услуг в реестр поставщиков образовательных услуг для организации «<?= $model->name ?>» отклонена. Оператор персонифицированного финансирования указал следующую причину:</p>

    <p style="background-color: yellow;"><?= $model->refuse_reason ?></p>
    <p>Если Вы в силах устранить причину отклонения заявки – отредактируйте ее, перейдя по ссылке <?= Html::a(Html::encode($updateLink), $updateLink) ?> или найдя ее по номеру заявки <span style="background-color: yellow;"><?= $model->anonymous_update_token ?></span></p>

    <p style="font-size: small;">Данное письмо отправлено автоматически, отвечать на него не нужно. Если же письмо получено Вами по ошибке, то это означает, что организация или индивидуальный предприниматель, который хотел попасть в реестр, описался в электронной почте. Это очень досадно. Но Вы, пожалуйста, просто игнорируйте данное письмо, как проигнорировали предыдущее. Спасибо и хорошего Вам настроения!</p>
</div>
