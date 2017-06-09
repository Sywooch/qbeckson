<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\statics\DirectoryProgramActivity */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="directory-program-activity-form">
    <?php $form = ActiveForm::begin(); ?>
    <?php if ($model->user) : ?>
        <p>Добавил пользователь: <?= $model->user->getUserName() ?></p>
    <?php endif; ?>
    <?= $form->field($model, 'name')->textInput() ?>
    <?= $form->field($model, 'status')->dropDownList($model::statuses()) ?>
    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Создать' : 'Редактировать',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
