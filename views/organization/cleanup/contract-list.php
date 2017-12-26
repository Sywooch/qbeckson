<?php

use app\widgets\SearchFilter;
use app\models\Contracts;
use app\helpers\GridviewHelper;
use app\models\UserIdentity;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;

/** @var $this yii\web\View */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $searchModel \app\models\ContractsSearch */

$this->title = 'Выбрать договор для удаления';
$this->params['breadcrumbs'][] = ['label' => 'Список запросов на удаление', 'url' => ['contract']];
$this->params['breadcrumbs'][] = $this->title;

$number = [
    'attribute' => 'number',
];
$date = [
    'attribute' => 'date',
    'format' => 'date',
];
$rezerv = [
    'attribute' => 'rezerv',
    'label' => 'Резерв',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
];
$start_edu_contract = [
    'attribute' => 'start_edu_contract',
    'format' => 'date',
    'label' => 'Действует с',
];
$stop_edu_contract = [
    'attribute' => 'stop_edu_contract',
    'format' => 'date',
    'label' => 'Действует до',
];
$group_id = [
    'attribute' => 'group_id',
    'value' => 'group.name',
];
$childFullName = [
    'attribute' => 'childFullName',
    'value' => 'certificate.fio_child',
    'label' => 'ФИО ребёнка'
];
$moduleName = [
    'attribute' => 'moduleName',
    'value' => 'year.fullname',
    'label' => 'Модуль'
];
$certificateNumber = [
    'attribute' => 'certificateNumber',
    'label' => 'Сертификат',
    'value' => function ($data) {
        return $data->certificate->number;
    }
];
$programName = [
    'attribute' => 'programName',
    'label' => 'Программа',
    'format' => 'raw',
    'value' => function ($data) {
        return $data->program->name;
    },
];
$payerName = [
    'attribute' => 'payerName',
    'label' => 'Плательщик',
    'format' => 'raw',
    'value' => function ($data) {
        return Html::a(
            $data->payers->name,
            Url::to(['payers/view', 'id' => $data->payer->id]),
            ['target' => '_blank', 'data-pjax' => '0']
        );
    }
];

$filterColumns = [
    $number,
];

$activeColumns = [
    [
        'attribute' => 'payer_id',
        'type' => SearchFilter::TYPE_HIDDEN
    ],
    $number,
    $date,
    $rezerv,
    [
        'attribute' => 'paid',
        'type' => SearchFilter::TYPE_RANGE_SLIDER,
    ],
    $start_edu_contract,
    $stop_edu_contract,
    $group_id,
    $childFullName,
    $moduleName,
    $certificateNumber,
    $programName,
    $payerName,
    [
        'attribute' => 'all_parents_funds',
        'type' => SearchFilter::TYPE_RANGE_SLIDER,
        'pluginOptions' => ['max' => 10000],
    ],
    [
        'class' => ActionColumn::className(),
        'template' => '{delete-order}',
        'searchFilter' => false,
        'buttons' => [
            'delete-order' => function ($url, $model) {
                /** @var Contracts $model */
                $option = [
                    'class' => 'btn btn-warning',
                    'title' => 'Направить запрос на удаление договора',
                    'data-toggle' => 'tooltip'
                ];
                return Html::a(
                    '<i class="glyphicon glyphicon-trash"></i>',
                    Url::to(['create', 'id' => $model->id]),
                    $option
                );
            },
        ],
    ],
];

$preparedActiveColumns = GridviewHelper::prepareColumns('contracts', $activeColumns, 'active');
?>

<div class="contracts">
    <?= SearchFilter::widget([
        'model' => $searchModel,
        'action' => ['contract-list'],
        'data' => GridviewHelper::prepareColumns(
            'contracts',
            $filterColumns,
            'active',
            'searchFilter',
            null
        ),
        'role' => UserIdentity::ROLE_ORGANIZATION,
        'type' => 'active'
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'pjax' => true,
        'summary' => false,
        'columns' => $preparedActiveColumns,
    ]); ?>
</div>
