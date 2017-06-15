<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\TouchSpin;
use kartik\slider\Slider;

/* @var $this yii\web\View */
/* @var $model app\models\search\CertificatesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="certificates-search search-form" style="display:none;">

    <?php $form = ActiveForm::begin([
        'action' => !empty($action) ? $action : ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?php echo $form->field($model, 'number') ?>
        </div>

        <div class="col-md-3">
            <?php echo $form->field($model, 'fio_child') ?>
        </div>

        <div class="col-md-12">
            <?php echo $form->field($model, 'nominalRange')->widget(Slider::classname(), [
                'sliderColor' => Slider::TYPE_GREY,
                'handleColor' => Slider::TYPE_DANGER,
                'pluginOptions' => [
                    'handle' => 'triangle',
                    'min' => 0,
                    'max' => 150000,
                    'step' => 10,
                    'range' => true
                ]
            ]); ?>
        </div>

        <div class="col-md-12">
            <?php echo $form->field($model, 'rezervRange')->widget(Slider::classname(), [
                'sliderColor' => Slider::TYPE_GREY,
                'handleColor' => Slider::TYPE_DANGER,
                'pluginOptions' => [
                    'handle' => 'triangle',
                    'min' => -100,
                    'max' => 150000,
                    'step' => 10,
                    'range' => true
                ]
            ]); ?>
        </div>

        <div class="col-md-12">
            <?php echo $form->field($model, 'balanceRange')->widget(Slider::classname(), [
                'sliderColor' => Slider::TYPE_GREY,
                'handleColor' => Slider::TYPE_DANGER,
                'pluginOptions' => [
                    'handle' => 'triangle',
                    'min' => 0,
                    'max' => 150000,
                    'step' => 10,
                    'range' => true
                ]
            ]); ?>
        </div>

        <div class="col-md-3">
            <?php echo $form->field($model, 'contractCount')->widget(TouchSpin::classname(), [
                'options' => ['placeholder' => $model->getAttributeLabel('contractCount')],
            ]); ?>
        </div>

        <div class="col-md-3">
            <?php echo $form->field($model, 'actual')->dropDownList([1 => 'Активен', 0 => 'Приостановлен'], ['prompt' => 'Выберите..']) ?>
        </div>

        <div class="col-md-12">
            <?= Html::submitButton('Начать поиск', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <br/>

    <?php ActiveForm::end(); ?>

</div>
