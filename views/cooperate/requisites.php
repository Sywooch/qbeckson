<?php

use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cooperate */
/* @var $payer app\models\Payers */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Заполнение реквизитов на заключение соглашения с уполномоченной организацией: ' . $model->payer->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cooperate-requisites col-md-10 col-md-offset-1">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="cooperate-requisites">
        <?php $form = ActiveForm::begin(['id' => 'cooperate-requisites-form']); ?>
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
        <div class="form-group">
            <?= Html::a('Назад', Url::to(['payers/view', 'id' => $model->payer->id]), ['class' => 'btn btn-primary']) ?>
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
