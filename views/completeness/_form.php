<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Completeness */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="completeness-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'completeness')->textInput() ?>

    <div class="form-group">
       <?= Html::a('Назад', [date('m') != 12 ? '/groups/invoice' : '/groups/dec'], ['class' => 'btn btn-primary']) ?>
&nbsp;
        <?= Html::submitButton($model->isNewRecord ? 'Сохранить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
