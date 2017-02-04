<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Disputes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="disputes-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php /* $form->field($model, 'contract_id')->textInput() ?>

    <?= $form->field($model, 'month')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'from')->textInput() */ ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6])->label(false) ?>
    
    <?php
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
     if (isset($roles['certificate'])) {
    echo $form->field($model, 'display')->checkbox(['value' => 1, 'ng-model' => 'edit']);  
    }
    ?>
    
    <div class="form-group">
        <?= Html::a('Назад', Url::to(['/contracts/view', 'id' => $contract]), ['class' => 'btn btn-primary']) ?>
        &nbsp;
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
