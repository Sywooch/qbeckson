<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProgrammeModule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="years-form">

    <?php $form = ActiveForm::begin(); ?>

      
    <label class="control-label">Нормативная стоимость&nbsp;</label><?= $model->normative_price; ?>
    
    <?= $form->field($model, 'price')->textInput() ?>

    <div class="form-group">
        <?= Html::a('Назад', Url::to(['/personal/organization-programs']), ['class' => 'btn btn-primary']); ?>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
