<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

//use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\models\contracts\GroupSwitcher */
/* @var $form yii\widgets\ActiveForm */
/* @var $groupsList array */

$this->title = 'Сменить группу';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="groups-form col-md-10 col-md-offset-1">

    <?php $form = ActiveForm::begin(['enableAjaxValidation' => true]); ?>

    <?= $form->field($model, 'group_id')->dropDownList($groupsList); ?>
    

    <div class="form-group">
        <?= Html::a('Назад', Url::to(['groups/contracts', 'id' => $model->group_id]), ['class' => 'btn btn-primary']) ?>
        &nbsp;
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
