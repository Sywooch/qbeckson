<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $nerfer \app\models\certificates\FreezeUnFreezeCertificate */

$this->title = 'Понизить номинал сертификата: ' . $nerfer->certificate->number;

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if ($roles['payer']) {
    $this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['/personal/payer-certificates']];
}
$this->params['breadcrumbs'][] = ['label' => $nerfer->certificate->number, 'url' => ['view', 'id' => $nerfer->certificate->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="certificates-update" ng-app>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($nerfer, 'newNominal'); ?>

    <?= Html::label('<p>' . Html::checkbox(null, null, ['id' => 'check-enshure', 'ng-model' => 'enshure']) . 'Внимание! Установление номинала сертификата, отличного от предусмотренного программой ПФ,</p><p> требует согласия родителя (законного представителя) ребенка. Нажимая на галочку,</p><p> Вы подтверждаете, что решение установления иного номинала продумано и согласовано.</p>', 'check-enshure') ?>
    <div class="form-group">

        <?= Html::a('Отменить', ['view', 'id' => $nerfer->certificate->id], ['class' => 'btn btn-danger']); ?>
        <?= Html::submitButton($nerfer->certificate->isNewRecord ? 'Создать' : 'Обновить',
            ['class' => $nerfer->certificate->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                'ng-show' => "enshure"]) ?>

    </div>

    <?php ActiveForm::end(); ?>


</div>
