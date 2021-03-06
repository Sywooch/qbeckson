<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\Organization */

$updateLink = Yii::$app->urlManager->createAbsoluteUrl(['organization/request-update', 'token' => $model->anonymous_update_token]);
?>
<div class="password-reset">
    <p>Здравствуйте!</p>

    <p>В системе персонифицированного финансирования дополнительного образования <?= Html::a(Html::encode(Yii::$app->urlManager->createAbsoluteUrl(['site/index'])), Yii::$app->urlManager->createAbsoluteUrl(['site/index'])) ?> успешно создана заявка на включение поставщика образовательных услуг в реестр поставщиков образовательных услуг для организации: «<?= $model->name ?>».</p>
    <p>Оператор рассмотрит заявку в ближайшее время (точно прогнозировать ввиду его загрузки сложно, но он будет стремиться соблюдать правила персонифицированного финансирования)</p>
    <p>Вы получите дополнительное уведомление о рассмотрении заявки на данную электронную почту.</p>
    <p>Вы можете отслеживать статус заявки по ссылке <?= Html::a(Html::encode($updateLink), $updateLink) ?> или запомнить и использовать номер заявки <span style="background-color: yellow;"><?= $model->anonymous_update_token ?></span> для ее поиска </p>
    <p style="font-size: small;">Данное письмо отправлено автоматически, отвечать на него не нужно. Если же письмо получено Вами по ошибке, то это означает, что организация или индивидуальный предприниматель, который хотел попасть в реестр, описался в электронной почте. Это очень досадно. Но Вы, пожалуйста, просто игнорируйте данное письмо и еще одно, которое придет Вам после рассмотрения оператором чужой, как оказалось заявки.</p>
</div>
