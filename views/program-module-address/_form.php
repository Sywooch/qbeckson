<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $addressModels app\models\ProgramModuleAddress[] */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="program-module-address-form">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper',
        'widgetBody' => '.container-items',
        'widgetItem' => '.item',
        'limit' => 10,
        'min' => 1,
        'insertButton' => '.add-item',
        'deleteButton' => '.remove-item',
        'model' => $addressModels[0],
        'formId' => 'dynamic-form',
        'formFields' => ['address'],
    ]); ?>
    <div class="container-items">
        <?php foreach ($addressModels as $i => $addressModel) : ?>
            <div class="item panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title pull-left">Адрес</h3>
                    <div class="pull-right">
                        <button type="button" class="add-item btn btn-success btn-xs">
                            <i class="glyphicon glyphicon-plus"></i>
                        </button>
                        <button type="button" class="remove-item btn btn-danger btn-xs">
                            <i class="glyphicon glyphicon-minus"></i>
                        </button>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <?= $form->field($addressModel, "[{$i}]address")->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php DynamicFormWidget::end(); ?>
    <div class="form-group">
        <?= Html::submitButton(
            empty($addressModels[0]->address) ? 'Добавить' : 'Обновить',
            ['class' => empty($addressModels[0]->address) ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
