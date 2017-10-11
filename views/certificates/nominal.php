<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Certificates */
/* @var $freezer \app\models\certificates\FreezeUnFreezeCertificate */

$this->title = 'Редактировать сертификат: ' . $model->number;

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if ($roles['operators']) {
    $this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['/personal/operator-certificates']];
}
if ($roles['payer']) {
    $this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['/personal/payer-certificates']];
}
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="certificates-update">

    <h1><?= Html::encode($this->title) ?></h1>
  <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nominal')->textInput(!$roles['payer'] ? ['readOnly'=>true] : ['maxlength' => true, 'id' => 'nom']) ?>

    <div class="form-group">
       <?php
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        if ($roles['payer']) {
            echo Html::a('Отменить', ['view', 'id' => $model->id], ['class' => 'btn btn-danger']);
        }
       ?>
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
