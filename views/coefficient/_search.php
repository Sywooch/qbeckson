<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CoefficientSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coefficient-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'p21v') ?>

    <?= $form->field($model, 'p21s') ?>

    <?= $form->field($model, 'p21o') ?>

    <?= $form->field($model, 'p22v') ?>

    <?php // echo $form->field($model, 'p22s') ?>

    <?php // echo $form->field($model, 'p22o') ?>

    <?php // echo $form->field($model, 'p3v') ?>

    <?php // echo $form->field($model, 'p3s') ?>

    <?php // echo $form->field($model, 'p3n') ?>

    <?php // echo $form->field($model, 'weekyear') ?>

    <?php // echo $form->field($model, 'weekmonth') ?>

    <?php // echo $form->field($model, 'pk') ?>

    <?php // echo $form->field($model, 'norm') ?>

    <?php // echo $form->field($model, 'potenc') ?>

    <?php // echo $form->field($model, 'ngr') ?>

    <?php // echo $form->field($model, 'sgr') ?>

    <?php // echo $form->field($model, 'vgr') ?>

    <?php // echo $form->field($model, 'chr1') ?>

    <?php // echo $form->field($model, 'zmr1') ?>

    <?php // echo $form->field($model, 'chr2') ?>

    <?php // echo $form->field($model, 'zmr2') ?>

    <?php // echo $form->field($model, 'blimrob') ?>

    <?php // echo $form->field($model, 'blimtex') ?>

    <?php // echo $form->field($model, 'blimest') ?>

    <?php // echo $form->field($model, 'blimfiz') ?>

    <?php // echo $form->field($model, 'blimxud') ?>

    <?php // echo $form->field($model, 'blimtur') ?>

    <?php // echo $form->field($model, 'blimsoc') ?>

    <?php // echo $form->field($model, 'ngrp') ?>

    <?php // echo $form->field($model, 'sgrp') ?>

    <?php // echo $form->field($model, 'vgrp') ?>

    <?php // echo $form->field($model, 'ppchr1') ?>

    <?php // echo $form->field($model, 'ppzm1') ?>

    <?php // echo $form->field($model, 'ppchr2') ?>

    <?php // echo $form->field($model, 'ppzm2') ?>

    <?php // echo $form->field($model, 'ocsootv') ?>

    <?php // echo $form->field($model, 'ocku') ?>

    <?php // echo $form->field($model, 'ocmt') ?>

    <?php // echo $form->field($model, 'obsh') ?>

    <?php // echo $form->field($model, 'ktob') ?>

    <?php // echo $form->field($model, 'vgs') ?>

    <?php // echo $form->field($model, 'sgs') ?>

    <?php // echo $form->field($model, 'pchsrd') ?>

    <?php // echo $form->field($model, 'pzmsrd') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
