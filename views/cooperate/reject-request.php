<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cooperate */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Отклонить заявку на заключение соглашения с уполномоченной организацией: ' . $model->organization->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cooperate-reject-request col-md-10 col-md-offset-1">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="cooperate-reject-request">
        <?php $form = ActiveForm::begin(['id' => 'cooperate-reject-request-form']); ?>
        <?= $form->field($model, 'reject_reason')->textarea(['rows' => 5]) ?>
        <div class="form-group">
            <?= Html::a('Назад', Url::to(['organization/view', 'id' => $model->organization_id]), ['class' => 'btn btn-primary']) ?>
            <?= Html::submitButton('Отклонить', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
