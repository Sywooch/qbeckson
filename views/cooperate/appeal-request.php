<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cooperate */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Подать жалобу на плательщика: ' . $model->payer->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cooperate-appeal-request col-md-10 col-md-offset-1">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="cooperate-appeal-request">
        <div class="well">
            <p class="lead">Причина отказа: <?= $model->reject_reason ?></p>
        </div>
        <?php $form = ActiveForm::begin(['id' => 'cooperate-appeal-request-form']); ?>
        <?= $form->field($model, 'appeal_reason')->textarea(['rows' => 5]) ?>
        <div class="form-group">
            <?= Html::a('Назад', Url::to(['payers/view', 'id' => $model->payer_id]), ['class' => 'btn btn-primary']) ?>
            <?= Html::submitButton('Подать жалобу', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
