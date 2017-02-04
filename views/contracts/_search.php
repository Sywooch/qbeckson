<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ContractsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contracts-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'number') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'status_termination') ?>

    <?php // echo $form->field($model, 'status_comment') ?>

    <?php // echo $form->field($model, 'status_year') ?>

    <?php // echo $form->field($model, 'link_doc') ?>

    <?php // echo $form->field($model, 'link_ofer') ?>

    <?php // echo $form->field($model, 'start_edu_programm') ?>

    <?php // echo $form->field($model, 'start_edu_contract') ?>

    <?php // echo $form->field($model, 'stop_edu_contract') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
