<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Help */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Удаление контрактов';
?>

<div class="help-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'organizationId')->textInput() ?>

    <?= $form->field($model, 'contractIdStart')->textInput() ?>

    <?= $form->field($model, 'contractIdFinish')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Запустить', ['btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
