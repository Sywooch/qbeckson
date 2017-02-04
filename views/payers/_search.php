<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PayersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payers-search">

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

    <?php // echo $form->field($model, 'directionality') ?>

    <?php // echo $form->field($model, 'directionality_1_count') ?>

    <?php // echo $form->field($model, 'directionality_2_count') ?>

    <?php // echo $form->field($model, 'directionality_3_count') ?>

    <?php // echo $form->field($model, 'directionality_4_count') ?>

    <?php // echo $form->field($model, 'directionality_5_count') ?>

    <?php // echo $form->field($model, 'directionality_6_count') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
