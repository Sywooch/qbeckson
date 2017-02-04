<?php
use yii\helpers\Url;
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use app\models\Organization;
use yii\helpers\ArrayHelper;
use kartik\form\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model app\models\Groups */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Сменить группу';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="groups-form col-md-10 col-md-offset-1">

    <?php $form = ActiveForm::begin(); ?>

       <?= $form->field($model, 'group_id')->dropDownList(ArrayHelper::map(app\models\Groups::find()->where(['organization_id' => $model->organization_id])->andwhere(['program_id' => $model->program_id])->andwhere(['year_id' => $model->year_id])->all(), 'id', 'name'))->label('Группа'); ?>
    

    <div class="form-group">
        <?= Html::a('Назад', Url::to(['groups/contracts', 'id' => $model->group_id]), ['class' => 'btn btn-primary']) ?>
        &nbsp;
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
