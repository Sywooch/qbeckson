<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Organization */

$this->title = 'Пересчитать лимит программы: ' . $model->name;

$this->params['breadcrumbs'][] = 'Организации';
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="organization-update col-md-10 col-md-offset-1">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'limit')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
