<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Operators */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="operators-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'OGRN')->textInput() ?>

    <?= $form->field($model, 'INN')->textInput() ?>

    <?= $form->field($model, 'KPP')->textInput() ?>

    <?= $form->field($model, 'OKPO')->textInput() ?>

    <?= $form->field($model, 'address_legal')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address_actual')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
