<?php

use app\components\widgets\postButtonWithModalConfirm\PostButtonWithModalConfirm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

?>
<div class="btn-row">
    <?php if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_OPERATOR)) {
        echo Html::a('К списку программ', '/personal/operator-programs', ['class' => 'btn btn-theme']);
        echo Html::a('Пересчитать нормативную стоимость', Url::to(['/programs/newnormprice', 'id' => $model->id]), ['class' => 'btn btn-theme']);
        echo Html::a('Пересчитать лимит', Url::to(['/programs/newlimit', 'id' => $model->id]), ['class' => 'btn btn-theme']);
        echo Html::a('Пересчитать рейтинг', Url::to(['/programs/raiting', 'id' => $model->id]), ['class' => 'btn btn-theme']);
    }

    if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_CERTIFICATE)) {
        echo Html::a('К списку программ', '/personal/certificate-programs', ['class' => 'btn btn-theme']);
    }

    if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_PAYER)) {
        echo Html::a('К списку программ', '/personal/payer-programs', ['class' => 'btn btn-theme']);
    }

    if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)) {
        echo Html::a('К списку программ', '/personal/organization-programs', ['class' => 'btn btn-theme']);
        if ($model->verification === \app\models\Programs::VERIFICATION_DONE) {
            echo Html::a($model->getPhoto() ? 'Изменить фото' : 'Добавить фото', ['add-photo', 'id' => $model->id], ['class' => 'btn btn-theme']);
            echo Html::a('Адреса программы', ['add-addresses', 'id' => $model->id], ['class' => 'btn btn-theme']);
        }

        if ($model->getLivingContracts()->exists() || $model->getModules()->andWhere(['open' => 1])->exists()) {

            echo \yii\bootstrap\Button::widget(['label'   => 'Редактировать Программу нельзя',
                                                'options' => ['class'    => 'btn btn-theme',
                                                              'disabled' => 'disabled'],]);
            echo \yii\bootstrap\Button::widget(['label'   => 'Удалить программу нельзя',
                                                'options' => ['class'    => 'btn btn-danger',
                                                              'disabled' => 'disabled'],]);
        } else {
            echo Html::a('Редактировать', Url::to(['/programs/update', 'id' => $model->id]), ['class' => 'btn btn-theme']);
            echo PostButtonWithModalConfirm::widget(['title'        => 'Удалить программу',
                                                     'url'          => Url::to(['/programs/delete', 'id' => $model->id]),
                                                     'confirm'      => 'Вы уверены, что хотите удалить программу?',
                                                     'toggleButton' => ['class' => 'btn btn-danger', 'label' => 'Удалить']]);

        }


    }

    echo Html::a('Посмотреть файл', '/' . $model->link, ['class' => 'btn btn-theme']);
    ?>
</div>

