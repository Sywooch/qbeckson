<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $cooperation app\models\Cooperate */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
Modal::begin([
    'header' => '<h2>Расторгнуть соглашение</h2>',
    'toggleButton' => [
        'tag' => 'a',
        'class' => 'btn btn-danger pull-right',
        'label' => 'Расторгнуть соглашение',
    ],
]);
?>
<p class="lead">Вы уверенны, что хотите расторнуть соглашение?</p>
<?php $form = ActiveForm::begin([
    'id' => 'cooperate-reject-contract-form',
    'action' => ['cooperate/reject-contract', 'id' => $cooperation->id]
]); ?>
<div class="form-group clearfix">
    <?= Html::submitButton('Расторгнуть', ['class' => 'btn btn-danger btn-block']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php Modal::end(); ?>
