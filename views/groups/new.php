<?php
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
?>

<div class="groups-form col-md-10 col-md-offset-1">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Для открытия программы надо создать группу</h4>
                
            </div>
            <div class="panel-body">
                 <?php /* DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 6, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelsGroups[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'name', 'address', 'schedule', 'datestart', 'datestop'
                    ],
                ]); ?>

                <div class="container-items"><!-- widgetContainer -->
                <?php foreach ($modelsGroups as $i => $modelGroup): ?>
                    <div class="item panel panel-default"><!-- widgetBody -->
                        <div class="panel-heading">
                            
                            <div class="pull-right">
                                <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <?php
                                // necessary for update action.
                                if (! $modelYears->isNewRecord) {
                                    echo Html::activeHiddenInput($modelGroup, "[{$i}]id");
                                }
                            ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <?= $form->field($modelGroup, "[{$i}]name")->textInput(['maxlength' => true]) ?>
                                    
                                    <?= $form->field($modelGroup, "[{$i}]address")->textarea(['rows' => 4]) ?>
    
                                    <?= $form->field($modelGroup, "[{$i}]schedule")->textarea(['rows' => 4]) ?>

                                    <?= $form->field($modelGroup, "[{$i}]datestart")->widget(DateControl::classname(), [
                                        'type'=>DateControl::FORMAT_DATE,
                                        'ajaxConversion'=>false,
                                        'options' => [
                                            'pluginOptions' => [
                                                'autoclose' => true
                                            ]
                                        ]
                                    ]) ?>

                                    <?= $form->field($modelGroup, "[{$i}]datestop")->widget(DateControl::classname(), [
                                        'type'=>DateControl::FORMAT_DATE,
                                        'ajaxConversion'=>false,
                                        'options' => [
                                            'pluginOptions' => [
                                                'autoclose' => true
                                            ]
                                        ]
                                    ]) ?>

                                </div>
                            </div><!-- .row -->
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end();  */ ?>
                
                <?= $form->field($modelsGroups, 'name')->textInput(['maxlength' => true]) ?>
    
                <?= $form->field($modelsGroups, 'address')->textarea(['rows' => 4]) ?>

                <?= $form->field($modelsGroups, 'schedule')->textarea(['rows' => 4]) ?>

                <?= $form->field($modelsGroups, 'datestart')->widget(DateControl::classname(), [
                    'type'=>DateControl::FORMAT_DATE,
                    'ajaxConversion'=>false,
                    'options' => [
                        'pluginOptions' => [
                            'autoclose' => true
                        ]
                    ]
                ]) ?>

                <?= $form->field($modelsGroups, 'datestop')->widget(DateControl::classname(), [
                    'type'=>DateControl::FORMAT_DATE,
                    'ajaxConversion'=>false,
                    'options' => [
                        'pluginOptions' => [
                            'autoclose' => true
                        ]
                    ]
                ]) ?>

            </div>
        </div>
    
    <div class="form-group">
        <?= Html::a('Назад', '/personal/organization-programs', ['class' => 'btn btn-primary']) ?>
        &nbsp;
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
