<?php

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
?>

<div class="groups-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
       $organizations = new Organization();
        $organization = $organizations->getOrganization();
    ?>

    <?= $form->field($model, 'program_id')->dropDownList(ArrayHelper::map(app\models\Programs::find()->where(['organization_id' => $organization['id']])->andwhere(['verification' => 2])->all(), 'id', 'name'), ['id'=>'prog-id', 'prompt'=>'-- Не выбрана --',]) ?>
    
    <?php // $form->field($model, 'year_id')->dropDownList(ArrayHelper::map(app\models\ProgrammeModule::find()->all(), 'id', 'year')) ?>
    
    <?= $form->field($model, 'year_id')->widget(DepDrop::classname(), [
        'options'=>['id'=>'year-id'],
        'pluginOptions'=>[
            'depends'=>['prog-id'],
            'placeholder'=>'-- Не выбран --',
            'url'=>Url::to(['/groups/year'])
        ]
    ])->label('Год')
    ?>
    
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'address')->textarea(['rows' => 4]) ?>
    
    <?= $form->field($model, 'schedule')->textarea(['rows' => 4]) ?>
    
    <?= $form->field($model, 'datestart')->widget(DateControl::classname(), [
        'type'=>DateControl::FORMAT_DATE,
        'ajaxConversion'=>false,
        'options' => [
            'pluginOptions' => [
                'autoclose' => true
            ]
        ]
    ]) ?>
    
    <?= $form->field($model, 'datestop')->widget(DateControl::classname(), [
        'type'=>DateControl::FORMAT_DATE,
        'ajaxConversion'=>false,
        'options' => [
            'pluginOptions' => [
                'autoclose' => true
            ]
        ]
    ]) ?>

    <div class="form-group">
       <?= Html::a('Отмена', '/personal/organization-groups', ['class' => 'btn btn-danger']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
