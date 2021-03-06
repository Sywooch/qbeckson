<?php

use app\components\widgets\postButtonWithModalConfirm\PostButtonWithModalConfirm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

?>
<div class="btn-row">
    <?php

    if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_CERTIFICATE)) {
        echo Html::a('К списку заданий', '/personal/certificate-programs', ['class' => 'btn btn-theme']);
    }

    if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_PAYER)) {
        echo Html::a('К списку заданий', '/personal/payer-municipal-task', ['class' => 'btn btn-theme']);
        if ($model->verification == \app\models\Programs::VERIFICATION_UNDEFINED || $model->verification == \app\models\Programs::VERIFICATION_WAIT) {
            echo Html::a('Одобрить', ['/programs/update-task', 'id' => $model->id], ['class' => 'btn btn-success']);
            echo Html::a('Отказать', ['/programs/decertificate', 'id' => $model->id], ['class' => 'btn btn-danger']);
        } elseif ($model->verification == \app\models\Programs::VERIFICATION_DONE) {
            echo Html::a('Перенести в другой реестр', ['/programs/update-task', 'id' => $model->id], ['class' => 'btn btn-warning']);
            if (!$model->getMunicipalTaskContracts(\app\models\MunicipalTaskContract::STATUS_ACTIVE)->count()) {
                echo Html::a('Убрать из реестра', ['/programs/decertificate', 'id' => $model->id], ['class' => 'btn btn-danger']);
            }
        }
    }

    if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)) {
        echo Html::a('К списку программ', '/personal/organization-municipal-task', ['class' => 'btn btn-theme']);
        if ($model->verification === \app\models\Programs::VERIFICATION_DONE) {
            echo Html::a($model->getPhoto() ? 'Изменить фото' : 'Добавить фото', ['add-photo', 'id' => $model->id], ['class' => 'btn btn-theme']);
            echo Html::a($model->addresses ? 'Изменить адреса для программы' : 'Указать адреса для программы', ['add-addresses', 'id' => $model->id], ['class' => 'btn btn-theme']);
        }

        if ($model->verification === \app\models\Programs::VERIFICATION_WAIT) {
            echo \app\components\widgets\ButtonWithInfo::widget([
                'label' => 'Редактировать',
                'message' => 'Невозможно. Организация запустила процедуру сертификации',
                'options' => ['disabled' => 'disabled',
                    'class' => 'btn btn-theme',]
            ]);
            echo \app\components\widgets\ButtonWithInfo::widget([
                'label' => 'Удалить',
                'message' => 'Невозможно. Организация запустила процедуру сертификации',
                'options' => ['disabled' => 'disabled',
                    'class' => 'btn btn-danger',]
            ]);

        } elseif ($model->getMunicipalTaskContracts()->count() || $model->getModules()->andWhere(['open' => 1])->exists()) {
            echo \app\components\widgets\ButtonWithInfo::widget([
                'label' => 'Редактировать',
                'message' => 'Невозможно, существуют контракты и/или открыто зачисление в одном или нескольких модулях, либо оператор запустил процедуру сертификации',
                'options' => ['disabled' => 'disabled',
                    'class' => 'btn btn-theme',]
            ]);
            echo \app\components\widgets\ButtonWithInfo::widget([
                'label' => 'Удалить',
                'message' => 'Невозможно, существуют контракты и/или открыто зачисление в одном или нескольких модулях',
                'options' => ['disabled' => 'disabled',
                    'class' => 'btn btn-danger',]
            ]);
        } else {
            echo Html::a('Редактировать', Url::to(['/programs/update', 'id' => $model->id]), ['class' => 'btn btn-theme']);
            echo PostButtonWithModalConfirm::widget(['title' => 'Удалить программу',
                'url' => Url::to(['/programs/delete', 'id' => $model->id]),
                'confirm' => 'Вы уверены, что хотите удалить программу?',
                'toggleButton' => ['class' => 'btn btn-danger', 'label' => 'Удалить']]);
            if ($model->canTaskBeTransferred) {
                echo Html::a('Перевести на ПФ', Url::to(['/programs/transfer-task', 'id' => $model->id]), ['class' => 'btn btn-warning']);
            }
        }
    }

    echo Html::a('Открыть текст программы', $model->programFile, ['class' => 'btn btn-theme']);
    ?>
</div>

