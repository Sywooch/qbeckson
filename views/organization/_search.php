<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrganizationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="organization-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'actual') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'license_date') ?>

    <?php // echo $form->field($model, 'license_number') ?>

    <?php // echo $form->field($model, 'license_issued') ?>

    <?php // echo $form->field($model, 'requisites') ?>

    <?php // echo $form->field($model, 'representative') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'geocode') ?>

    <?php // echo $form->field($model, 'max_child') ?>

    <?php // echo $form->field($model, 'amount_child') ?>

    <?php // echo $form->field($model, 'inn') ?>

    <?php // echo $form->field($model, 'okopo') ?>

    <?php // echo $form->field($model, 'raiting') ?>

    <?php // echo $form->field($model, 'ground') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
