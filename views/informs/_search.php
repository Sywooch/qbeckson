<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\InformsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="informs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'program_id') ?>

    <?= $form->field($model, 'contract_id') ?>

    <?= $form->field($model, 'from') ?>

    <?= $form->field($model, 'text') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'read') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
