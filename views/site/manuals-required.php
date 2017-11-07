<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Для продолжения работы в системе подтвердите, что Вы ознакомились с основными правилами работы в ней';
?>
<div class="site-manual row">
    <div class="col-md-10 col-md-offset-1">
        <h3><?= $this->title ?></h3><br/>
        <?php
        $form = ActiveForm::begin();
        $nextShow = true;
        ?>
        <?php foreach ($models as $index => $model): ?>
            <div class="checkbox-container" style="display: <?= ($nextShow) ? 'block' : 'none'; ?>">
                <?= $form->field($model, "[$index]checked")->checkbox(['onClick' => 'showNextContainer(this);'])->label($model->getAttributeLabel('checked')) ?>
            </div>
            <?php $nextShow = $model->checked ? $nextShow : false; ?>
        <?php endforeach; ?>
        <div class="checkbox-container save-button" style="display: none;">
            <?= Html::submitButton('Сохранить и продолжить', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>