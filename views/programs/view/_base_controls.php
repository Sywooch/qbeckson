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
        echo Html::a(
            'Пересчитать нормативную стоимость',
            Url::to(['/programs/newnormprice', 'id' => $model->id]),
            ['class' => 'btn btn-theme']
        );
        echo Html::a(
            'Пересчитать лимит',
            Url::to(['/programs/newlimit', 'id' => $model->id]),
            ['class' => 'btn btn-theme']
        );
        echo Html::a(
            'Пересчитать рейтинг',
            Url::to(['/programs/raiting', 'id' => $model->id]),
            ['class' => 'btn btn-theme']
        );
    }

    if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_CERTIFICATE)) {
        echo Html::a('К списку программ', '/personal/certificate-programs', ['class' => 'btn btn-theme']);
    }

    if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_PAYER)) {
        echo Html::a('К списку программ', '/personal/payer-programs', ['class' => 'btn btn-theme']);
        if ($model->isMunicipalTask) {
            echo Html::a(
                'Изменить параметры',
                ['/programs/update-task', 'id' => $model->id],
                ['class' => 'btn btn-theme']
            );
        }
    }

    if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)) {
        $contractsExist = $model->getLivingContracts()->exists();
        $openModulesExist = $model->getModules()->andWhere(['open' => 1])->exists();
        $transferButtonTitle = 'Перевести на мунзадание';

        echo Html::a('К списку программ', '/personal/organization-programs', ['class' => 'btn btn-theme']);
        if ($model->verification === \app\models\Programs::VERIFICATION_DONE) {
            echo Html::a(
                $model->getPhoto() ? 'Изменить фото' : 'Добавить фото',
                ['add-photo', 'id' => $model->id],
                ['class' => 'btn btn-theme']
            );
            echo Html::a(
                $model->addresses ? 'Изменить адреса для программы' : 'Указать адреса для программы',
                ['add-addresses', 'id' => $model->id], ['class' => 'btn btn-theme']
            );
        }

        if ($model->verification === \app\models\Programs::VERIFICATION_WAIT) {
            echo \app\components\widgets\ButtonWithInfo::widget([
                'label' => 'Редактировать',
                'message' => 'Невозможно. Оператор запустил процедуру сертификации',
                'options' => ['disabled' => 'disabled',
                    'class' => 'btn btn-theme',]
            ]);
            echo \app\components\widgets\ButtonWithInfo::widget([
                'label' => 'Удалить',
                'message' => 'Невозможно. Оператор запустил процедуру сертификации',
                'options' => ['disabled' => 'disabled',
                    'class' => 'btn btn-danger',]
            ]);
        } elseif ($contractsExist || $openModulesExist) {
            echo \app\components\widgets\ButtonWithInfo::widget([
                'label' => 'Редактировать',
                'message' => 'Невозможно, существуют контракты и/или открыто зачисление'
                    . ' в одном или нескольких модулях, либо оператор запустил процедуру сертификации',
                'options' => ['disabled' => 'disabled',
                    'class' => 'btn btn-theme',]
            ]);
            echo \app\components\widgets\ButtonWithInfo::widget([
                'label' => 'Удалить',
                'message' => 'Невозможно, существуют контракты и/или открыто зачисление в одном или нескольких модулях',
                'options' => ['disabled' => 'disabled',
                    'class' => 'btn btn-danger',]
            ]);
            echo \app\components\widgets\ButtonWithInfo::widget([
                'label' => $transferButtonTitle,
                'message' => $contractsExist ? 'Перевести программу невозможно.'
                    . ' Есть действующие договоры или заявки на программу.'
                    : 'Перевести программу невозможно. Открыто зачисление.',
                'options' => ['disabled' => 'disabled',
                    'class' => 'btn btn-warning',]
            ]);
        } else {
            echo Html::a(
                'Редактировать',
                Url::to(['/programs/update', 'id' => $model->id]),
                ['class' => 'btn btn-theme']
            );
            echo PostButtonWithModalConfirm::widget(['title' => 'Удалить программу',
                'url' => Url::to(['/programs/delete', 'id' => $model->id]),
                'confirm' => 'Вы уверены, что хотите удалить программу?',
                'toggleButton' => ['class' => 'btn btn-danger', 'label' => 'Удалить']]);
            if ($model->canProgrammeBeTransferred) {
                echo PostButtonWithModalConfirm::widget([
                    'title' => $transferButtonTitle,
                    'askPassword' => false,
                    'url' => Url::to(['/programs/transfer-programme', 'id' => $model->id]),
                    'confirm' => 'Вы собираетесь перевести программу'
                        . ' на муниципальное задание в реестр "Ожидающие рассмотрения"',
                    'toggleButton' => ['class' => 'btn btn-warning', 'label' => $transferButtonTitle]
                ]);
            }
        }
    }
    echo Html::a('Открыть текст программы', $model->programFile, ['class' => 'btn btn-theme']);
    ?>
</div>

