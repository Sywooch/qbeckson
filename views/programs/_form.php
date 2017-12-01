<?php

use app\models\Mun;
use app\models\ProgrammeModule;
use app\models\statics\DirectoryProgramActivity;
use app\models\statics\DirectoryProgramDirection;
use kartik\field\FieldRange;
use kartik\file\FileInput;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use kartik\widgets\DepDrop;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */
/* @var $form yii\widgets\ActiveForm */

$js = <<<JS
const wrapper = jQuery(".dynamicform_wrapper");
const panelTitle = jQuery(".dynamicform_wrapper .panel-title"); 
wrapper.on("afterInsert", function(e, item) {
    $(".dynamicform_wrapper").find('input[id$="kvfirst"]').each(function() {
        $(this).val('Педагог, обладающий соответствующей квалификацией');      
    });
    $(".dynamicform_wrapper").find('.panel-title').each(function(index) {
        jQuery(this).html((index + 1) + " модуль")
    });
});

wrapper.on("afterDelete", function(e) {
   panelTitle.each(function(index) {
        jQuery(this).html((index + 1) + " модуль")
    });
});
JS;
$url = Url::to(['activity/add-activity']);
$changeJS = <<<JS
function() {
                        var isNew = $(this).find('[data-select2-tag="true"]');
                        var name = isNew.val();
                        var directionId = $('#direction-id').val();
                        if (isNew.length && name !== "...") {
                            $.ajax({
                            	type: "POST",
                            	url: "$url",
                            	data: {name: name, directionId: directionId},
                            	success: function (id) {
                            	    isNew.replaceWith('<option selected value=' + id +'>' + name + '</option>');
                            	},
                            	error: function (xhr, ajaxOptions, thrownError) { }
                            });
                        }
                    }
JS;



$this->registerJs($js);
?>
<div class="programs-form" ng-app>
    <?php $form = ActiveForm::begin([
        'id' => 'dynamic-form',
        'action' => !empty($strictAction) ? $strictAction : null,
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>
    <?php
    if ($model->inTransferProcess) {
        echo $form->field($model, 'inTransferProcess')->hiddenInput()->label(false);
        echo $form->field($model, 'is_municipal_task')->hiddenInput()->label(false);
    }
    ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?php
        echo $form->field($model, 'direction_id')->widget(Select2::class, [
            'data' => ArrayHelper::map(DirectoryProgramDirection::findAllRecords(), 'id', 'name'),
            'options' => [
                'placeholder' => 'Выберите направленность программы ...',
                'id' => 'direction-id'
            ],
        ]);
        echo $form->field($model, 'activity_ids')->widget(DepDrop::class, [
            'data'=> $model->direction_id ?
                ArrayHelper::map(
                    DirectoryProgramActivity::findAllActiveActivitiesByDirection($model->direction_id),
                    'id',
                    'name'
                ) : [],
            'type' => DepDrop::TYPE_SELECT2,
            'options' => [
                'multiple' => true,
            ],
            'select2Options' => [
                'showToggleAll' => false,
                'pluginOptions' => [
                    'tags' => true,
                    'maximumSelectionLength' => 3,
                ],
                'pluginEvents' => [
                    'change' => new \yii\web\JsExpression($changeJS),
                ],
            ],
            'pluginOptions' => [
                'placeholder' => 'Выберите вид (виды) деятельности или добавьте свой',
                'depends' => ['direction-id'],
                'url' => Url::to(['activity/load-activities']),
                'loadingText' => 'Загрузка видов деятельности..',
            ],
        ]);
    ?>
    <?= $form->field($model, 'form')->dropDownList(Yii::$app->params['form']) ?>
    <?= $form->field($model, 'mun')->dropDownList(ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name')) ?>
    <?= $form->field($model, 'ground')->dropDownList(Yii::$app->params['ground']) ?>
    <?= $form->field($model, 'annotation')->textarea(['rows' => 5]) ?>
    <?= $form->field($model, 'task')->textarea(['rows' => 5]) ?>
    <?php
    if ($model->isNewRecord) {
        echo $form->field($file, 'docFile')->widget(FileInput::class, ['pluginOptions' => [
            'showPreview' => false,
            'showCaption' => true,
            'showRemove' => true,
            'showUpload' => false
        ]]);
    } else {
        echo '<a href="' . $model->programFile . '"><span class="glyphicon glyphicon-download-alt"></span> Скачать программу</a>';
        echo $form->field($file, 'newprogram')->checkbox(['value' => 1, 'ng-model' => 'newprogram']);
        echo '<div ng-show="newprogram">';
        echo $form->field($file, 'docFile')->widget(FileInput::class, ['pluginOptions' => [
            'showPreview' => false,
            'showCaption' => true,
            'showRemove' => true,
            'showUpload' => false
        ]]);
        echo '</div>';
    }
    ?>

    <?= FieldRange::widget([
        'form' => $form,
        'model' => $model,
        'label' => 'Возрастная категория детей, определяемая минимальным и максимальным возрастом лиц, которые могут быть зачислены на обучение по образовательной программе',
        'attribute1' => 'age_group_min',
        'attribute2' => 'age_group_max',
        'type' => FieldRange::INPUT_SPIN,
        'separator' => 'до',
        'fieldConfig1' => ['addon' => [
            'prepend' => ['content' => 'От '],
        ]],
    ]);
    ?>

    <?php // $form->field($model, 'age_group_min')->textInput() ?>

    <?php // $form->field($model, 'age_group_max')->textInput() ?>

    <?= $form->field($model, 'ovz')->dropDownList([1 => 'Без ОВЗ', 2 => 'С ОВЗ'], ['onChange' => 'selectOvz(this.value);']) ?>

    <div id="zab" style="display: <?= $model->ovz == 2 ? 'block' : 'none' ?>">
        <?= $form->field($model, 'zab')->checkboxList(\app\models\Programs::illnesses()) ?>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>Добавление модулей реализации программы</h4>

        </div>
        <div class="panel-body">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsYears[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'year',
                    'month',
                ],
            ]); ?>

            <div class="container-items"><!-- widgetContainer -->
                <?php foreach ($modelsYears as $i => $modelYears): ?>
                    <div class="item panel panel-default"><!-- widgetBody -->
                        <div class="panel-heading">
                            <h3 class="panel-title pull-left"><?= $i + 1 ?> модуль</h3>
                            <div class="pull-right">
                                <button type="button" class="add-item btn btn-success btn-xs"><i
                                            class="glyphicon glyphicon-plus"></i></button>
                                <button type="button" class="remove-item btn btn-danger btn-xs"><i
                                            class="glyphicon glyphicon-minus"></i></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <?php
                            // necessary for update action.
                            if (!$modelYears->isNewRecord) {
                                echo Html::activeHiddenInput($modelYears, "[{$i}]id");
                            }
                            ?>
                            <div class="row">
                                <div class="col-sm-12">

                                    <?= $form->field($modelYears, "[{$i}]name")->textInput(['maxlength' => true]) ?>

                                    <?= $form->field($modelYears, "[{$i}]month")->textInput(['maxlength' => true]) ?>

                                    <?= $form->field($modelYears, "[{$i}]hours")->textInput(['maxlength' => true]) ?>

                                    <?= $form->field($modelYears, "[{$i}]kvfirst", ['options' => ['class' => 'input-title', 'data' => ['input-title' => 'Укажите необходимую квалификацию педагогического работника. Не рекомендуем указывать конкретные данные, так как при, например, увольнении педагога, будет сложно найти соответствующего программе преподавателя с тем же стажем работы или фамилией. <br>Пример: Педагог, обладающий соответствующей квалификацией']]])
                                        ->textInput(['maxlength' => true, 'placeholder' => 'Педагог, обладающий соответствующей квалификацией']) ?>

                                    <?php if ($modelYears->scenario != ProgrammeModule::SCENARIO_MUNICIPAL_TASK) {
                                        echo $form->field($modelYears, "[{$i}]hoursindivid")->textInput(['maxlength' => true]);
                                        echo $form->field($modelYears, "[{$i}]hoursdop")->textInput(['maxlength' => true]);
                                        echo $form->field($modelYears, "[{$i}]kvdop")->textInput(['maxlength' => true]);
} ?>

                                    <?= $form->field($modelYears, "[{$i}]minchild")->textInput() ?>

                                    <?= $form->field($modelYears, "[{$i}]maxchild")->textInput() ?>

                                    <?= $form->field($modelYears, "[{$i}]results")->textarea() ?>
                                </div>
                            </div><!-- .row -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>
    </div>

    <?= $form->field($model, 'norm_providing')->textarea(['rows' => 5]) ?>

    <?php
    if ($model->isMunicipalTask && 0) {
        echo $form->field($model, 'certificate_accounting_limit');
    }

    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);

    if (!$model->isNewRecord && !isset($roles['operators']) && !$model->inTransferProcess) {
        echo $form->field($model, 'edit')->checkbox(['value' => 1, 'ng-model' => 'edit']);
        echo '<div class="form-group" ng-show="edit">';
        echo Html::a('Отменить', $model->isMunicipalTask ? ['/personal/organization-municipal-task'] : ['/personal/organization-programs'], ['class' => 'btn btn-danger']);
        echo '&nbsp';
        echo Html::submitButton($model->isNewRecord ? 'Отправить программу на сертификацию' : 'Обновить программу', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
        echo '</div>';
    } else {
        echo '<div class="form-group">';
        echo Html::a('Отменить', $model->isMunicipalTask ? ['/personal/organization-municipal-task'] : ['/personal/organization-programs'], ['class' => 'btn btn-danger']);
        echo '&nbsp';
        echo Html::submitButton($model->isNewRecord ? ($model->isMunicipalTask ? 'Создать программу' : 'Отправить программу на сертификацию') : ($model->inTransferProcess ? 'Перевести программу на ПФ' : 'Обновить программу'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
        echo '</div>';
    }
    ?>

    <?php ActiveForm::end(); ?>

</div>
