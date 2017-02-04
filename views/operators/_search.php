<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OperatorsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="operators-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'OGRN') ?>

    <?= $form->field($model, 'INN') ?>

    <?php // echo $form->field($model, 'KPP') ?>

    <?php // echo $form->field($model, 'OKPO') ?>

    <?php // echo $form->field($model, 'address_legal') ?>

    <?php // echo $form->field($model, 'address_actual') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'position') ?>

    <?php // echo $form->field($model, 'fio') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
