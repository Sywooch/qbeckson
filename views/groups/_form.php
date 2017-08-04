<?php

use app\models\GroupClass;
use kartik\widgets\TimePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Organization;
use kartik\datecontrol\DateControl;
use kartik\widgets\DepDrop;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Groups */
/* @var $form yii\widgets\ActiveForm */

$js = <<<'JS'
    $('.show-form-options').change(function() {
        var $this = $(this);
        if($this.is(":checked")) {
            $this.closest('.form-checker').next('.form-options').slideDown();
        } else {
            $this.closest('.form-checker').next('.form-options').slideUp();
        }    
    });
JS;
$this->registerJs($js, $this::POS_READY);
?>

<div class="groups-form">
    <?php $form = ActiveForm::begin(); ?>
    <?php
        $organizations = new Organization();
        $organization = $organizations->getOrganization();
    ?>

    <?= $form->field($model, 'program_id')
        ->dropDownList(
            ArrayHelper::map(
                app\models\Programs::find()
                    ->where(['organization_id' => $organization['id']])
                    ->andWhere(['verification' => 2])
                    ->all(),
                'id',
                'name'
            ),
            ['id' => 'prog-id', 'prompt' => '-- Не выбрана --',]
        ) ?>

    <?= $form->field($model, 'year_id')->widget(DepDrop::class, [
        'options' => ['id' => 'module-id'],
        'pluginOptions' => [
            'depends' => ['prog-id'],
            'placeholder' => '-- Не выбран --',
            'url' => Url::to(['groups/year'])
        ]
    ])->label('Модуль')
    ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <hr>
    <?php foreach (GroupClass::weekDays() as $i => $week) : ?>
        <div class="row form-checker">
            <div class="col-md-12">
                <?= $form->field($groupClasses[$i], "[{$i}]status")
                    ->checkbox([
                        'label' => $week,
                        'class' => 'show-form-options'
                    ])->label(false); ?>
            </div>
        </div>
        <div class="form-options" style="display: none">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($groupClasses[$i], "[{$i}]address")->widget(DepDrop::class, [
                        'pluginOptions' => [
                            'depends' => ['module-id'],
                            'placeholder' => '-- Не выбран --',
                            'url' => Url::to(['groups/select-addresses'])
                        ]
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($groupClasses[$i], "[{$i}]classroom")
                        ->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <?= $form->field($groupClasses[$i], "[{$i}]time_from")
                        ->widget(TimePicker::class, [
                            'pluginOptions' => [
                                'showMeridian' => false,
                                'minuteStep' => 5,
                            ]
                        ]) ?>
                </div>
                <div class="col-md-5">
                    <?= $form->field($groupClasses[$i], "[{$i}]time_to")
                        ->widget(TimePicker::class, [
                            'pluginOptions' => [
                                'showMeridian' => false,
                                'minuteStep' => 5,
                            ]
                        ]) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($groupClasses[$i], "[{$i}]hours_count")
                        ->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <hr>
        </div>
    <?php endforeach; ?>


    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'datestart')->widget(DateControl::class, [
                'type'=>DateControl::FORMAT_DATE,
                'ajaxConversion'=>false,
                'options' => [
                    'pluginOptions' => [
                        'autoclose' => true
                    ]
                ]
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'datestop')->widget(DateControl::class, [
                'type' => DateControl::FORMAT_DATE,
                'ajaxConversion' => false,
                'options' => [
                    'pluginOptions' => [
                        'autoclose' => true
                    ]
                ]
            ]) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::a('Отмена', ['personal/organization-groups'], ['class' => 'btn btn-danger']) ?>
        <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
