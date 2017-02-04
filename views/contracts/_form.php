<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contracts-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'certificate_id')->textInput() ?>

    <?= $form->field($model, 'program_id')->textInput() ?>

    <?= $form->field($model, 'organization_id')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'status_termination')->textInput() ?>

    <?= $form->field($model, 'status_comment')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status_year')->textInput() ?>

    <?= $form->field($model, 'link_doc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'link_ofer')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'group_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'all_funds')->textInput() ?>

    <?= $form->field($model, 'funds_cert_1')->textInput() ?>

    <?= $form->field($model, 'funds_cert_2')->textInput() ?>

    <?= $form->field($model, 'funds_cert_3')->textInput() ?>

    <?= $form->field($model, 'funds_cert_4')->textInput() ?>

    <?= $form->field($model, 'funds_cert_5')->textInput() ?>

    <?= $form->field($model, 'funds_cert_6')->textInput() ?>

    <?= $form->field($model, 'funds_cert_7')->textInput() ?>

    <?= $form->field($model, 'funds_cert_8')->textInput() ?>

    <?= $form->field($model, 'funds_cert_9')->textInput() ?>

    <?= $form->field($model, 'funds_cert_10')->textInput() ?>

    <?= $form->field($model, 'funds_cert_11')->textInput() ?>

    <?= $form->field($model, 'funds_cert_12')->textInput() ?>

    <?= $form->field($model, 'funds_1')->textInput() ?>

    <?= $form->field($model, 'funds_2')->textInput() ?>

    <?= $form->field($model, 'funds_3')->textInput() ?>

    <?= $form->field($model, 'funds_4')->textInput() ?>

    <?= $form->field($model, 'funds_5')->textInput() ?>

    <?= $form->field($model, 'funds_6')->textInput() ?>

    <?= $form->field($model, 'funds_7')->textInput() ?>

    <?= $form->field($model, 'funds_8')->textInput() ?>

    <?= $form->field($model, 'funds_9')->textInput() ?>

    <?= $form->field($model, 'funds_10')->textInput() ?>

    <?= $form->field($model, 'funds_11')->textInput() ?>

    <?= $form->field($model, 'funds_12')->textInput() ?>

    <?= $form->field($model, 'all_parents_funds')->textInput() ?>

    <?= $form->field($model, 'parents_funds_1')->textInput() ?>

    <?= $form->field($model, 'parents_funds_2')->textInput() ?>

    <?= $form->field($model, 'parents_funds_3')->textInput() ?>

    <?= $form->field($model, 'parents_funds_4')->textInput() ?>

    <?= $form->field($model, 'parents_funds_5')->textInput() ?>

    <?= $form->field($model, 'parents_funds_6')->textInput() ?>

    <?= $form->field($model, 'parents_funds_7')->textInput() ?>

    <?= $form->field($model, 'parents_funds_8')->textInput() ?>

    <?= $form->field($model, 'parents_funds_9')->textInput() ?>

    <?= $form->field($model, 'parents_funds_10')->textInput() ?>

    <?= $form->field($model, 'parents_funds_11')->textInput() ?>

    <?= $form->field($model, 'parents_funds_12')->textInput() ?>

    <?= $form->field($model, 'start_edu_programm')->textInput() ?>

    <?= $form->field($model, 'funds_gone')->textInput() ?>

    <?= $form->field($model, 'stop_edu_contract')->textInput() ?>

    <?= $form->field($model, 'start_edu_contract')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
