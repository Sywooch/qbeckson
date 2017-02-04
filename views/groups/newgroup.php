<?php
use yii\helpers\Url;
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use app\models\Organization;
use yii\helpers\ArrayHelper;
use kartik\form\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model app\models\Groups */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Добавить группу';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="groups-form col-md-10 col-md-offset-1">

    <?php $form = ActiveForm::begin(); ?>

   
                
                <?= $form->field($modelsGroups, 'name')->textInput(['maxlength' => true]) ?>
    
                <?= $form->field($modelsGroups, 'address')->textarea(['rows' => 4]) ?>

                <?= $form->field($modelsGroups, 'schedule')->textarea(['rows' => 4]) ?>

                <?= $form->field($modelsGroups, 'datestart')->widget(DateControl::classname(), [
                    'type'=>DateControl::FORMAT_DATE,
                    'ajaxConversion'=>false,
                    'options' => [
                        'pluginOptions' => [
                            'autoclose' => true
                        ]
                    ]
                ]) ?>

                <?= $form->field($modelsGroups, 'datestop')->widget(DateControl::classname(), [
                    'type'=>DateControl::FORMAT_DATE,
                    'ajaxConversion'=>false,
                    'options' => [
                        'pluginOptions' => [
                            'autoclose' => true
                        ]
                    ]
                ]) ?>


    
    <div class="form-group">
        <?= Html::a('Назад', Url::to(['programs/view', 'id' => $modelsGroups->program_id]), ['class' => 'btn btn-primary']) ?>
        &nbsp;
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
