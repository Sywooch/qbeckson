<?php

use app\models\GroupClass;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\time\TimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Groups */
/* @var $groupClasses \app\models\GroupClass */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Добавить группу';
$this->params['breadcrumbs'][] = $this->title;
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
<div class="groups-form col-md-10 col-md-offset-1">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <hr>
    <?php foreach ($groupClasses as $i => $class) : ?>
        <div class="form-checker">
            <?= $form->field($groupClasses[$i], "[{$i}]status")
                ->checkbox([
                    'label' => $class->week_day,
                    'class' => 'checkbox show-form-options'
                ])->label(false); ?>
        </div>
        <div class="form-options" style="display: none">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($groupClasses[$i], "[{$i}]address")
                        ->dropDownList($programModuleAddresses) ?>
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
                'type' => DateControl::FORMAT_DATE,
                'ajaxConversion' => false,
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
        <?= Html::a(
            'Назад',
            Url::to(['programs/view', 'id' => $model->program_id]),
            ['class' => 'btn btn-primary']
        ) ?>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
