<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Organization */

$this->title = 'Пересчитать нормативную стоимость: ';

$this->params['breadcrumbs'][] = 'Организации';
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="organization-update col-md-10 col-md-offset-1">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($modelYears, 'p21z')->dropDownList([1 => 'Высшая', 2 => 'Первая', 3 => 'Иная']) ?>
    
    <?= $form->field($modelYears, 'p22z')->dropDownList([1 => 'Высшая', 2 => 'Первая', 3 => 'Иная']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
