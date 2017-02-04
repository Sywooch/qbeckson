<?php

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
    
    <?= $form->field($model, 'address')->textarea(['rows' => 4]) ?>
    
    <?= $form->field($model, 'schedule')->textarea(['rows' => 4]) ?>
    
    <?php
    
    $contracts = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['group_id' => $model->id])
                    ->andWhere(['status' => [0,1,3]])
                    ->count(); 
    
    if ($contracts == 0) {
    
        echo $form->field($model, 'datestart')->widget(DateControl::classname(), [
            'type'=>DateControl::FORMAT_DATE,
            'ajaxConversion'=>false,
            'options' => [
                'pluginOptions' => [
                    'autoclose' => true
                ]
            ]
        ]);
    
        echo $form->field($model, 'datestop')->widget(DateControl::classname(), [
            'type'=>DateControl::FORMAT_DATE,
            'ajaxConversion'=>false,
            'options' => [
                'pluginOptions' => [
                    'autoclose' => true
                ]
            ]
        ]);
    }
    ?>
    
    <div class="form-group">
       <?= Html::a('Отмена', '/personal/organization-groups', ['class' => 'btn btn-danger']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
