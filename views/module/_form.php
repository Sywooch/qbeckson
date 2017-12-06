<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProgrammeModule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="programme-module-form" ng-app>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'month')->textInput() ?>

    <?= $form->field($model, 'hours')->textInput() ?>

    <?= $form->field($model, 'kvfirst')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kvdop')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hoursindivid')->textInput() ?>

    <?= $form->field($model, 'hoursdop')->textInput() ?>

    <?= $form->field($model, 'minchild')->textInput() ?>

    <?= $form->field($model, 'maxchild')->textInput() ?>

    <?= $form->field($model, 'p21z')->textInput() ?>

    <?= $form->field($model, 'p22z')->textInput() ?>

    <?= $form->field($model, 'results')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?php
        echo $form->field($model, 'edit')->checkbox(['value' => 1, 'ng-model' => 'edit']);
        echo '<div class="form-group" ng-show="edit">';

        echo '&nbsp';
        echo Html::submitButton('Отправить модуль на сертификацию', ['class' => 'btn btn-success']);
        echo '</div>';
        echo Html::a('Отменить', ['/programms/view', 'id' => $model->id], ['class' => 'btn btn-danger']);
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
