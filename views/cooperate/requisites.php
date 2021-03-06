<?php

use app\models\Cooperate;
use kartik\datecontrol\DateControl;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cooperate */
/* @var $form yii\widgets\ActiveForm */
$model->scenario = Cooperate::SCENARIO_REQUISITES;
?>
<?php
Modal::begin([
    'header' => '<h2>Реквизиты соглашения</h2>',
    'toggleButton' => [
        'tag' => 'a',
        'class' => !is_null($toggleButtonClass) ? $toggleButtonClass : 'btn btn-primary',
        'label' => $label,
    ],
]);
?>
    <?php $form = ActiveForm::begin([
        'id' => 'cooperate-requisites-form',
        'action' => ['cooperate/requisites', 'id' => $model->id]
    ]); ?>
        <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'date')->widget(DateControl::class, [
            'type' => DateControl::FORMAT_DATE,
            'ajaxConversion' => false,
            'options' => [
                'pluginOptions' => [
                    'autoclose' => true
                ]
            ]
        ]) ?>
        <div class="form-group clearfix">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-success pull-right']) ?>
        </div>
    <?php ActiveForm::end(); ?>
<?php Modal::end(); ?>
