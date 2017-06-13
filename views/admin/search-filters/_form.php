<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\SettingsSearchFilters */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="settings-search-filters-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'table_name')->dropDownList(array_combine(Yii::$app->db->schema->tableNames, Yii::$app->db->schema->tableNames), ['prompt' => 'Выберите..', 'id' => 'table-name']) ?>

    <?= $form->field($model, 'table_columns')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'is_active')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
