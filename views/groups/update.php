<?php

use kartik\time\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model app\models\Groups */

$this->title = 'Редактировать группу: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Группы', 'url' => ['/personal/organization-groups']];
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="groups-update col-md-10 col-md-offset-1">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <hr>
    <?php foreach ($model->classes as $key => $class) : ?>
        <p class="lead"><?= $class->week_day ?></p>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($class, "[{$key}]address")
                    ->dropDownList($programModuleAddresses) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($class, "[{$key}]classroom")
                    ->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($class, "[{$key}]hours_count")
                    ->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($class, "[{$key}]time_from")
                    ->widget(TimePicker::class, [
                        'pluginOptions' => [
                            'showMeridian' => false,
                            'minuteStep' => 5,
                        ]
                    ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($class, "[{$key}]time_to")
                    ->widget(TimePicker::class, [
                        'pluginOptions' => [
                            'showMeridian' => false,
                            'minuteStep' => 5,
                        ]
                    ]) ?>
            </div>
        </div>
        <hr>
    <?php endforeach; ?>
    <?php
    $contracts = (new \yii\db\Query())
        ->select(['id'])
        ->from('contracts')
        ->where(['group_id' => $model->id])
        ->andWhere(['status' => [0,1,3]])
        ->count();
    
    if ($contracts === 0) {
        echo $form->field($model, 'datestart')->widget(DateControl::class, [
            'type' => DateControl::FORMAT_DATE,
            'ajaxConversion' => false,
            'options' => [
                'pluginOptions' => [
                    'autoclose' => true
                ]
            ]
        ]);
    
        echo $form->field($model, 'datestop')->widget(DateControl::class, [
            'type' => DateControl::FORMAT_DATE,
            'ajaxConversion' => false,
            'options' => [
                'pluginOptions' => [
                    'autoclose' => true
                ]
            ]
        ]);
    }
    ?>
    <div class="form-group">
        <?= Html::a('Отмена', ['/personal/organization-groups'], ['class' => 'btn btn-danger']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
