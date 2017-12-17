<?php

use app\models\Cooperate;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cooperate */
/* @var $form yii\widgets\ActiveForm */
$model->scenario = Cooperate::SCENARIO_LIMIT;
?>
<?php
Modal::begin([
    'header' => '<h2>Изменение суммы</h2>',
    'toggleButton' => [
        'tag' => 'a',
        'class' => !is_null($toggleButtonClass) ? $toggleButtonClass : 'btn btn-default',
        'label' => !is_null($label) ? $label : round($model->total_payment_limit, 2) . ' рублей',
    ],
]);
?>
    <?php $form = ActiveForm::begin([
        'id' => 'cooperate-limit-form',
        'action' => ['cooperate/payment-limit', 'id' => $model->id]
    ]); ?>
        <?= $form->field($model, 'total_payment_limit') ?>
        <div class="form-group clearfix">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-success pull-right']) ?>
        </div>
    <?php ActiveForm::end(); ?>
<?php Modal::end(); ?>
