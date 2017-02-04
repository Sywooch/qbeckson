<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = 'Параметры программы: '.$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/organization-programs']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>
        
        <?= $form->field($model, 'price', ['addon' => ['append' => ['content'=>'руб.']]])->textInput(['maxlength' => true]) ?>    
        
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
