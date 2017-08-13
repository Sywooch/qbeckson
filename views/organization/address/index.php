<?php

use mirocow\yandexmaps\Canvas;
use mirocow\yandexmaps\Map;
use mirocow\yandexmaps\objects\Placemark;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $models \app\models\OrganizationAddress[] */

$this->title = 'Адреса реализации образовательных программ';
$this->params['breadcrumbs'][] = $this->title;

$js = <<<'JS'
    $(document).on('change', '.address-checkbox', function () {
        var $this = $(this);
        var boxes = $('.address-checkbox');
        if($this.is(":checked")) {
            boxes.prop('checked', false);
            $this.prop('checked', true);
        }
    });
    /**
    * Не работает почему-то
    * 
    * $(".dynamicform_wrapper").on("beforeDelete", function () {
    *     if (!confirm("Вы уверенны, что хотите удалить этот элемент?")) {
    *         return false;
    *     }
    *     return true;
    * });
    * 
    */
JS;
$this->registerJs($js, $this::POS_READY);
?>
<div class="organization-address-index">
    <?php $form = ActiveForm::begin([
        'id' => 'dynamic-form'
    ]); ?>
    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper',
        'widgetBody' => '.container-items',
        'widgetItem' => '.item',
        'limit' => 999,
        'min' => 1,
        'insertButton' => '.add-item',
        'deleteButton' => '.remove-item',
        'model' => $models[0],
        'formId' => 'dynamic-form',
        'formFields' => ['address'],
    ]); ?>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title pull-left">Адрес</h3>
                <div class="pull-right">
                    <button type="button" class="add-item btn btn-success btn-xs">
                        <i class="glyphicon glyphicon-plus"></i>
                    </button>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-body container-items">
                <?php foreach ($models as $i => $model) : ?>
                    <div class="row item">
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <?= $form->field($model, "[{$i}]status")->checkbox([
                                    'label' => 'Основной адрес',
                                    'class' => 'address-checkbox'
                                ]) ?>
                            </div>
                            <div class="col-md-9">
                                <?php
                                if (!$model->isNewRecord) {
                                    echo Html::activeHiddenInput($model, "[{$i}]id");
                                }
                                ?>
                                <?= $form->field($model, "[{$i}]address")->textInput(['maxlength' => true])
                                    ->label(false) ?>
                            </div>
                            <div class="col-md-1">
                                <button style="margin-top: 5px;" type="button" class="remove-item btn btn-danger btn-xs">
                                    <i class="glyphicon glyphicon-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php DynamicFormWidget::end(); ?>
    <p><strong>*</strong> Для того, чтобы убедиться, что адрес верно будет отображаться на карте нажмите кнопку сохранить и проверьте туда ли поставлен ориентир.</p>
    <?php if (null !== $models[0]->address) : ?>
        <?php
        $marks = [];
        foreach ($models as $model) {
            $marks[] = new Placemark(
                [$model->lat, $model->lng],
                ['hintContent' => $model->address],
                ['iconColor' => '#1F99FF']
            );
        }
        echo Canvas::widget([
            'htmlOptions' => [
                'style' => 'height: 500px; width: 800px;',
                'class' => 'center-block'
            ],
            'map' => new Map(
                'yandex_map',
                [
                    'center' => [$models[0]->lat, $models[0]->lng],
                    'zoom' => 12,
                    'behaviors' => ['default', 'scrollZoom'],
                    'type' => 'yandex#map',
                ],
                [
                    'minZoom' => 3,
                    'maxZoom' => 20,
                    'controls' => [
                        "new ymaps.control.TypeSelector(['yandex#map', 'yandex#satellite'])",
                    ],
                    'objects' => $marks
                ]
            ),
        ]); ?>
    <?php endif; ?>
    <br>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
