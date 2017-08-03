<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use mirocow\yandexmaps\Map;
use mirocow\yandexmaps\Canvas;
use mirocow\yandexmaps\objects\Placemark;

/* @var $this yii\web\View */
/* @var $model \app\models\forms\SelectModuleMainAddressForm */
/* @var $programModuleModel \app\models\ProgrammeModule */

$this->title = 'Изменить оснойной адрес';
$this->params['breadcrumbs'][] = [
    'label' => 'Программа',
    'url' => ['programs/view', 'id' => $programModuleModel->program_id]
];
$this->params['breadcrumbs'][] = [
    'label' => 'Редактирование адресов',
    'url' => ['program-module-address/update', 'moduleId' => $programModuleModel->id]
];
$this->params['breadcrumbs'][] = $this->title;
$map = new Map(
    'yandex_map',
    [
        'center' => [$programModuleModel->mainAddress->lat, $programModuleModel->mainAddress->lng],
        'zoom' => 10,
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
);
?>
<div class="program-module-address-create">
    <div class="program-module-address-form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'addressId')
            ->dropDownList(
                ArrayHelper::map($programModuleModel->addresses, 'id', 'address')
            ) ?>
        <div class="form-group">
            <?= Html::submitButton('Изменить', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <?= Canvas::widget([
        'htmlOptions' => [
            'style' => 'height: 500px; width: 800px;',
            'class' => 'center-block'
        ],
        'map' => $map,
    ]); ?>
</div>
