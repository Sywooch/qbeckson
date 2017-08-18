<?php

use app\models\Cooperate;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cooperate */
/* @var $form yii\widgets\ActiveForm */

$model->scenario = Cooperate::SCENARIO_APPEAL;
?>
<?php
Modal::begin([
    'header' => '<h2>Подать жалобу</h2>',
    'toggleButton' => [
        'tag' => 'a',
        'class' => 'btn btn-danger pull-right',
        'label' => 'Подать жалобу',
    ],
]);
?>
<div class="well">
    <p class="lead">Причина отказа: <?= $model->reject_reason ?></p>
</div>
    <?php $form = ActiveForm::begin([
        'id' => 'cooperate-appeal-request-form',
        'action' => ['cooperate/appeal-request', 'id' => $model->id]
    ]); ?>
        <?= $form->field($model, 'appeal_reason')->textarea(['rows' => 5]) ?>
        <div class="form-group clearfix">
            <?= Html::submitButton('Подать жалобу', ['class' => 'btn btn-success pull-right']) ?>
        </div>
    <?php ActiveForm::end(); ?>
<?php Modal::end(); ?>
