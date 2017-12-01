<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProgrammeModule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="programme-module-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'month')->textInput() ?>

    <?= $form->field($model, 'hours')->textInput() ?>

    <?= $form->field($model, 'kvfirst')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kvdop')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hoursindivid')->textInput() ?>

    <?= $form->field($model, 'hoursdop')->textInput() ?>

    <?= $form->field($model, 'minchild')->textInput() ?>

    <?= $form->field($model, 'maxchild')->textInput() ?>

    <?= $form->field($model, 'p21z')->textInput() ?>

    <?= $form->field($model, 'p22z')->textInput() ?>

    <?= $form->field($model, 'results')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Create' : 'Update',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
