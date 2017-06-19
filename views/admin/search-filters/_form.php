<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SettingsSearchFilters */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="settings-search-filters-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'table_name')->dropDownList(array_combine(Yii::$app->db->schema->tableNames, Yii::$app->db->schema->tableNames), ['prompt' => 'Выберите..']) ?>

    <?= $form->field($model, 'table_columns')->textInput() ?>

    <?= $form->field($model, 'inaccessible_columns')->textInput() ?>

    <?= $form->field($model, 'is_active')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
