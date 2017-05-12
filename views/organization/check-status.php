<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Organization */

$this->title = 'Проверка статуса заявки';
?>
<div class="organization-create  col-md-10 col-md-offset-1">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['method' => 'get']); ?>
    <br />
    <?= Html::label('Номер заявки:') ?>
    <?= Html::textInput('token', null, ['class' => 'form-control']) ?>
    <br />
    <div class="form-group">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
