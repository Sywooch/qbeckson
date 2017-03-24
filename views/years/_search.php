<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProgrammeModuleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="years-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'program_id') ?>

    <?= $form->field($model, 'verification') ?>

    <?= $form->field($model, 'year') ?>

    <?= $form->field($model, 'month') ?>

    <?php // echo $form->field($model, 'hours') ?>

    <?php // echo $form->field($model, 'kvfirst') ?>

    <?php // echo $form->field($model, 'kvdop') ?>

    <?php // echo $form->field($model, 'hoursindivid') ?>

    <?php // echo $form->field($model, 'hoursdop') ?>

    <?php // echo $form->field($model, 'minchild') ?>

    <?php // echo $form->field($model, 'maxchild') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'normative_price') ?>

    <?php // echo $form->field($model, 'rating') ?>

    <?php // echo $form->field($model, 'limits') ?>

    <?php // echo $form->field($model, 'open') ?>

    <?php // echo $form->field($model, 'quality_control') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
