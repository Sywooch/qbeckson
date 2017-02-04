<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model app\models\Cooperate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cooperate-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->widget(DateControl::classname(), [
                                    'type'=>DateControl::FORMAT_DATE,
                                    'ajaxConversion'=>false,
                                    'options' => [
                                        'pluginOptions' => [
                                            'autoclose' => true
                                        ]
                                    ]
                                ]) ?>

    <div class="form-group">
       
         <?= Html::a('Назад', Url::to(['payers/view', 'id' => $id]), ['class' => 'btn btn-primary']) ?>
         &nbsp;
        <?= Html::submitButton($model->isNewRecord ? 'Сохранить' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
