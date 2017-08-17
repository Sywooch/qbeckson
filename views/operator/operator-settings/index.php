<?php

use app\models\Cooperate;
use kartik\widgets\DatePicker;
use trntv\filekit\widget\Upload;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\OperatorSettings */

$this->title = 'Параметры системы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operator-settings-create">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model) ?>
    <?= $form->field($model, 'generalDocument')->widget(Upload::class, [
        'url' => ['file-storage/upload'],
        'maxFileSize' => 1 * 1024 * 1024,
        'acceptFileTypes' => new JsExpression('/(\.|\/)(doc|docx)$/i'),
    ]); ?>
    <?= $form->field($model, 'extendDocument')->widget(Upload::class, [
        'url' => ['file-storage/upload'],
        'maxFileSize' => 1 * 1024 * 1024,
        'acceptFileTypes' => new JsExpression('/(\.|\/)(doc|docx)$/i'),
    ]); ?>
    <?= $form->field($model, 'document_name')->dropDownList(Cooperate::documentNames()) ?>
    <hr />
    <div class="row">
        <div class="col-md-12">
            <p><strong>Текущий период реализации программы ПФ</strong></p>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'current_program_date_from')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'current_program_date_to')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]) ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <p><strong>Будущий период реализации программы ПФ</strong></p>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'future_program_date_from')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'future_program_date_to')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(
            'Задать',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
