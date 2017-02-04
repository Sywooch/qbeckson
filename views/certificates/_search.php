<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CertificatesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="certificates-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'number') ?>

    <?= $form->field($model, 'payer_id') ?>

    <?= $form->field($model, 'actual') ?>

    <?php // echo $form->field($model, 'fio_child') ?>

    <?php // echo $form->field($model, 'fio_parent') ?>

    <?php // echo $form->field($model, 'nominal') ?>

    <?php // echo $form->field($model, 'balance') ?>

    <?php // echo $form->field($model, 'contracts') ?>

    <?php // echo $form->field($model, 'directivity1') ?>

    <?php // echo $form->field($model, 'directivity2') ?>

    <?php // echo $form->field($model, 'directivity3') ?>

    <?php // echo $form->field($model, 'directivity4') ?>

    <?php // echo $form->field($model, 'directivity5') ?>

    <?php // echo $form->field($model, 'directivity6') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
