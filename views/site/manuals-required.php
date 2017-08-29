<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */

$this->title = $model->name;
?>
<div class="site-manual row">
    <div class="col-md-10 col-md-offset-1">
        <h3>Для продолжения работы в системе подтвердите, что Вы работаете в ней не вслепую</h3>
        <?php $form = ActiveForm::begin(); ?>
        <?php foreach ($models as $index => $model): ?>
            <?= $form->field($model, "[$index]checked")->checkbox()->label($model->getAttributeLabel('checked')) ?>
        <?php endforeach; ?>
        <?= Html::submitButton('Сохранить и продолжить', ['class' => 'btn btn-primary']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>