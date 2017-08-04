<?php

use mirocow\yandexmaps\Canvas;
use mirocow\yandexmaps\Map;
use mirocow\yandexmaps\objects\Placemark;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $addressModels app\models\ProgramModuleAddress[] */
/* @var $form yii\widgets\ActiveForm */
/* @var $programModuleModel \app\models\ProgrammeModule */

$js = <<<'JS'
    $(document).on('change', '.address-checkbox', function () {
        var $this = $(this);
        var boxes = $('.address-checkbox');
        if($this.is(":checked")) {
            boxes.prop('checked', false);
            $this.prop('checked', true);
        } 
    })
JS;
$this->registerJs($js, $this::POS_READY);
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
                <?php foreach ($addressModels as $i => $addressModel) : ?>
                    <div class="row item">
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <?= $form->field($addressModel, "[{$i}]status")
                                    ->checkbox([
                                        'label' => 'Основной адрес',
                                        'class' => 'address-checkbox'
                                    ])->label(false) ?>
                            </div>
                            <div class="col-md-9">
                                <?php
                                if (!$addressModel->isNewRecord) {
                                    echo Html::activeHiddenInput($addressModel, "[{$i}]id");
                                }
                                ?>
                                <?= $form->field($addressModel, "[{$i}]address")->textInput(['maxlength' => true])->label(false) ?>
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
    <?php if (null !== $programModuleModel->mainAddress) : ?>
        <?= Canvas::widget([
            'htmlOptions' => [
                'style' => 'height: 500px; width: 800px;',
                'class' => 'center-block'
            ],
            'map' => new Map(
                'yandex_map',
                [
                    'center' => [$programModuleModel->mainAddress->lat, $programModuleModel->mainAddress->lng],
                    'zoom' => 15,
                    'behaviors' => ['default', 'scrollZoom'],
                    'type' => 'yandex#map',
                ],
                [
                    'minZoom' => 5,
                    'maxZoom' => 20,
                    'controls' => [
                        "new ymaps.control.TypeSelector(['yandex#map', 'yandex#satellite'])",
                    ],
                    'objects' => [
                        new Placemark(
                            [$programModuleModel->mainAddress->lat, $programModuleModel->mainAddress->lng],
                            [
                                'balloonContentBody' => 'Text',
                                'hintContent' => 'Подробнее',
                                'clusterCaption' => 'Caption',
                            ],
                            [
                                'iconColor' => '#1F99FF',
                            ]
                        )
                    ]
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
