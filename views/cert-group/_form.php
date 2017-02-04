<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CertGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cert-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'group')->textInput(['maxlength' => true, 'readOnly'=>true]) ?>

    <?= $form->field($model, 'nominal')->textInput() ?>

    <div class="form-group">
      <?php 
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
       if ($roles['payer']) {
            echo Html::a('Отменить', '/cert-group/index', ['class' => 'btn btn-danger']);
        }
        ?>
        <?= Html::submitButton('Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
