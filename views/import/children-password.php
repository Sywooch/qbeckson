<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Изменение паролей сертификатов';
?>
<div class="import-form">
    <h1><?= $this->title ?></h1>
    <?php $form = ActiveForm::begin() ?>

    <?= $form->field($model, 'importFile')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Изменить пароли', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>
