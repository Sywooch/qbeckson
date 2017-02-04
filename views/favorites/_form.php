<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Favorites */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="favorites-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'certificate_id')->textInput() ?>

    <?= $form->field($model, 'program_id')->textInput() ?>

    <?= $form->field($model, 'organization_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
