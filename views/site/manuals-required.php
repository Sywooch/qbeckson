<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */

$this->title = $model->name;
?>
<div class="site-manual row">
    <div class="col-md-10 col-md-offset-1">
        <h3>Для продолжения работы в системе подтвердите, что Вы работаете в ней не вслепую</h3>
        <?php
            $form = ActiveForm::begin();
            $i = 0;
        ?>
        <?php foreach ($models as $index => $model): ?>
            <div class="checkbox-container" style="display: <?= (!$i++ || $model->checked > 0) ? 'block' : 'none'; ?>">
                <?= $form->field($model, "[$index]checked")->checkbox(['onClick' => 'showNextContainer(this);'])->label($model->getAttributeLabel('checked')) ?>
            </div>
        <?php endforeach; ?>
        <div class="checkbox-container save-button" style="display: none;">
            <?= Html::submitButton('Сохранить и продолжить', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>