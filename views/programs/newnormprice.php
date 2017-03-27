<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = 'Сертифицировать программу';
$this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/organization-programs']];
$this->params['breadcrumbs'][] = $this->title;
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

?>

<div class="programs-form"  ng-app>


    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

        <?= $form->field($model, 'p3z')->dropDownList([1 => 'Высокое обеспечение', 2 => 'Среднее обеспечение', 3 => 'Низкое обеспечение']) ?> 
        
        
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
                        'p21z',
                        'p22z',
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
                                    
                                    <?= $form->field($modelYears, "[{$i}]p21z")->dropDownList([1 => 'Выше среднего', 2 => 'Средняя', 3 => 'Ниже среднего']) ?>
                                    
                                    <?= $form->field($modelYears, "[{$i}]p22z")->dropDownList([1 => 'Выше среднего', 2 => 'Средняя', 3 => 'Ниже среднего']) ?>
                                    
                                    <?= $form->field($modelYears, "[{$i}]normative_price", ['addon' => ['append' => ['content'=> Html::a('Изменить', Url::to(['/programs/normpricesave', 'id' => $modelYears->id]), ['class' => 'btn btn-success']), 
            'asButton' => true]]])->textInput(['readOnly'=>true]) ?>

                                </div>
                            </div><!-- .row -->
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
    <?php 
            echo Html::a('Назад', Url::to(['/programs/view', 'id' => $model->id]), ['class' => 'btn btn-primary']);
            echo '&nbsp;';
            echo Html::submitButton('Пересчитать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
            echo '&nbsp;';
            echo Html::a('Продолжить', Url::to(['/programs/view', 'id' => $model->id]), ['class' => 'btn btn-primary']);
        ?>

    <?php ActiveForm::end(); ?>
</div>
