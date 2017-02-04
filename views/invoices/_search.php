<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\InvoicesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoices-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'month') ?>

    <?= $form->field($model, 'organization_id') ?>

    <?= $form->field($model, 'payers_id') ?>

    <?= $form->field($model, 'sum') ?>

    <?php // echo $form->field($model, 'number') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'link') ?>

    <?php // echo $form->field($model, 'prepayment') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
