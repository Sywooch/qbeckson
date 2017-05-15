<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\Organization */

?>
<div class="password-reset">
    <p>Здравствуйте!</p>

    <p>Оператор рассмотрел Вашу заявку на включение в систему персонифицированного финансирования и теперь организация «<?= $model->name ?>» включена в реестр поставщиков образовательных услуг в рамках системы персонифицированного финансирования дополнительного образования детей.</p>

    <p>Для организации в системе <?= Html::a(Html::encode(Yii::$app->urlManager->createAbsoluteUrl(['site/index'])), Yii::$app->urlManager->createAbsoluteUrl(['site/index'])) ?> создан личный кабинет:</p>

    <table>
        <tr>
            <td>Имя пользователя</td>
            <td><span style="background-color: yellow;"><?= $model->user->username ?></span></td>
        </tr>
        <tr>
            <td>Пароль</td>
            <td><span style="background-color: yellow;"><?= $password ?></span></td>
        </tr>
    </table>

    <p>Теперь Вы можете зайти в свой личный кабинет, и навести там порядок. Рекомендуем начать с просмотра обучающих видеороликов, доступ к которым Вы получите в правом верхнем углу личного кабинета.</p>

    <p style="font-size: small;">Данное письмо отправлено автоматически, отвечать на него не нужно. Если же письмо получено Вами по ошибке, то это означает, что организация или индивидуальный предприниматель, который хотел попасть в реестр, описался в электронной почте. Это очень досадно. Но Вы, пожалуйста, просто игнорируйте данное письмо, как проигнорировали предыдущее. Спасибо и хорошего Вам настроения!</p>
</div>
