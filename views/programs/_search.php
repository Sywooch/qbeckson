<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProgramsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="programs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'program_id') ?>

    <?= $form->field($model, 'organization_id') ?>

    <?= $form->field($model, 'verification') ?>

    <?= $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'directivity') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'normative_price') ?>

    <?php // echo $form->field($model, 'rating') ?>

    <?php // echo $form->field($model, 'limit') ?>

    <?php // echo $form->field($model, 'study') ?>

    <?php // echo $form->field($model, 'open') ?>

    <?php // echo $form->field($model, 'goal') ?>

    <?php // echo $form->field($model, 'task') ?>

    <?php // echo $form->field($model, 'annotation') ?>

    <?php // echo $form->field($model, 'hours') ?>

    <?php // echo $form->field($model, 'ovz') ?>

    <?php // echo $form->field($model, 'quality_control') ?>

    <?php // echo $form->field($model, 'link') ?>

    <?php // echo $form->field($model, 'certification_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
