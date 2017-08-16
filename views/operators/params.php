<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Operators */

$this->title = 'Редактировать параметры';
$this->params['breadcrumbs'][] = ['label' => 'Оператор', 'url' => ['/personal/operator-statistic']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operators-update col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="operators-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'date_year_start')->widget(\yii\jui\DatePicker::classname(), [
            'language' => 'ru',
            'dateFormat' => 'yyyy-MM-dd',
        ]) ?>

        <?= $form->field($model, 'date_year_end')->widget(\yii\jui\DatePicker::classname(), [
            'language' => 'ru',
            'dateFormat' => 'yyyy-MM-dd',
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
