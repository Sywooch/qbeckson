<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CooperateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cooperate-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'organization_id') ?>

    <?= $form->field($model, 'payer_id') ?>

    <?= $form->field($model, 'number') ?>

    <?= $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'date_dissolution') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'reade') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
