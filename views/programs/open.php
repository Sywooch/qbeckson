<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;


/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title").each(function(index) {
        jQuery(this).html((index + 1) + " модуль")
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title").each(function(index) {
        jQuery(this).html((index + 1) + " модуль")
    });
});
';

$this->registerJs($js);

$this->title = 'Установить цену: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/organization-programs']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Установить цену';
?>
<div class="programs-update">

    <h1><?= Html::encode($this->title) ?></h1>

     <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

        
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Цена программы по модулям</h4>
                
            </div>
            <div class="panel-body">
                 <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 6, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelsYears[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'price',
                    ],
                ]); ?>

                <div class="container-items"><!-- widgetContainer -->
                <?php foreach ($modelsYears as $i => $modelYears): ?>
                    <div class="item panel panel-default"><!-- widgetBody -->
                        <div class="panel-heading">
                            <h3 class="panel-title pull-left"><?= $i + 1 ?> модуль</h3>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <?php
                                // necessary for update action.
                                if (! $modelYears->isNewRecord) {
                                    echo Html::activeHiddenInput($modelYears, "[{$i}]id");
                                }
                            ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <?= $form->field($modelYears, "[{$i}]price")->textInput(['maxlength' => true]) ?>
                                    
                                </div>
                            </div><!-- .row -->
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
        
        <?php 
            echo '<div class="form-group">';
                echo Html::submitButton($model->isNewRecord ? 'Отправить программу на сертификацию' : 'Обновить программу', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
            echo '</div>';
        
        ?>        

    <?php ActiveForm::end(); ?>


</div>
