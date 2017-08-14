<?php

use app\models\Cooperate;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cooperate */
/* @var $form yii\widgets\ActiveForm */

$title = 'Отклонить заявку на заключение соглашения с уполномоченной организацией: ' . $model->organization->name;

$model->scenario = Cooperate::SCENARIO_REJECT;
?>
<?php
Modal::begin([
    'header' => '<h2>Отклонить заявку</h2>',
    'toggleButton' => [
        'tag' => 'a',
        'class' => 'btn btn-danger pull-right',
        'label' => 'Отказать',
    ],
]);
?>
    <?php $form = ActiveForm::begin([
        'id' => 'cooperate-reject-request-form',
        'action' => ['cooperate/reject-request', 'id' => $model->id]
    ]); ?>
        <?= $form->field($model, 'reject_reason')->textarea(['rows' => 5]) ?>
        <div class="form-group clearfix">
            <?= Html::submitButton('Отклонить', ['class' => 'btn btn-success pull-right']) ?>
        </div>
    <?php ActiveForm::end(); ?>
<?php Modal::end(); ?>
