<?php

use app\models\Cooperate;
use kartik\datecontrol\DateControl;
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
        'class' => 'btn btn-default',
        'label' => $model->total_payment_limit . ' рублей',
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
