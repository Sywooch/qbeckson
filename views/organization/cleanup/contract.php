<?php

use kartik\grid\GridView;
use app\widgets\SearchFilter;
use app\helpers\GridviewHelper;
use app\models\UserIdentity;
use app\models\ContractDeleteApplication;
use app\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $waitingModel \app\models\search\ContractDeleteApplicationSearch */
/** @var $confirmedModel \app\models\search\ContractDeleteApplicationSearch */
/** @var $refusedModel \app\models\search\ContractDeleteApplicationSearch */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $dataConfirmedProvider \yii\data\ActiveDataProvider */
/** @var $dataRefusedProvider \yii\data\ActiveDataProvider */

$this->title = 'Список запросов на удаление';
$this->params['breadcrumbs'][] = $this->title;

$waitingColumns = $refusedColumns = [
    [
        'attribute' => 'contractNumber',
        'value' => function (ContractDeleteApplication $data) {
            return ArrayHelper::getValue($data, ['contract', 'number']);
        }
    ],
    [
        'attribute' => 'contractDate',
        'format' => 'date',
        'value' => function (ContractDeleteApplication $data) {
            return ArrayHelper::getValue($data, ['contract', 'date']);
        }
    ],
    [
        'attribute' => 'fileUrl',
        'format' => 'html',
        'value' => function(ContractDeleteApplication $data) {
            return Html::a($data->getFileUrl(), $data->getFileUrl());
        }
    ],
    [
        'attribute' => 'certificateNumber',
        'value' => function (ContractDeleteApplication $data) {
            return ArrayHelper::getValue($data, ['contract', 'certificate', 'number']);
        }
    ],
    [
        'attribute' => 'created_at',
        'format' => 'datetime',
    ],
];
$confirmedColumns = [
    [
        'attribute' => 'contract_number',
    ],
    [
        'attribute' => 'contract_date',
        'format' => 'date',
    ],
    [
        'attribute' => 'fileUrl',
        'format' => 'html',
        'value' => function(ContractDeleteApplication $data) {
            return Html::a($data->getFileUrl(), $data->getFileUrl());
        }
    ],
    [
        'attribute' => 'certificate_number',
    ],
    [
        'attribute' => 'created_at',
        'format' => 'datetime',
    ],
    [
        'attribute' => 'confirmed_at',
        'format' => 'datetime'
    ],
    [
        'attribute' => 'reason'
    ]
];
$refusedColumns[] = ['attribute' => 'confirmed_at', 'format' => 'datetime'];
$refusedColumns[] = ['attribute' => 'reason'];
$waitingColumns[] = ['attribute' => 'reason'];

$filterColumns = [['attribute' => 'contractNumber']];


$preparedWaitingColumns = GridviewHelper::prepareColumns($waitingModel::tableName(), $waitingColumns, 'waiting');
$preparedConfirmedColumns = GridviewHelper::prepareColumns($confirmedModel::tableName(), $confirmedColumns, 'confirmed');
$preparedRefusedColumns = GridviewHelper::prepareColumns($refusedModel::tableName(), $refusedColumns, 'refused');

$items = [
    [
        'label' => 'Ожидающие запросы на удаление (' . $dataProvider->count . ')',
        'active' => true,
        'content' => SearchFilter::widget([
                'model' => $waitingModel,
                'action' => ['contract'],
                'data' => GridviewHelper::prepareColumns(
                    $waitingModel::tableName(),
                    $filterColumns,
                    'waiting',
                    'searchFilter',
                    null
                ),
                'role' => UserIdentity::ROLE_ORGANIZATION,
                'type' => 'waiting',
                'customizable' => false
            ]) .
            GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => $preparedWaitingColumns,
            ])
    ],
    [
        'label' => 'Удаленные договоры (' . $dataConfirmedProvider->count . ')',
        'content' => SearchFilter::widget([
                'model' => $confirmedModel,
                'action' => ['contract'],
                'data' => GridviewHelper::prepareColumns(
                    $confirmedModel::tableName(),
                    [['attribute' => 'contract_number']],
                    'confirmed',
                    'searchFilter',
                    null
                ),
                'role' => UserIdentity::ROLE_ORGANIZATION,
                'type' => 'confirmed',
                'customizable' => false
            ]) .
            GridView::widget([
            'dataProvider' => $dataConfirmedProvider,
            'columns' => $preparedConfirmedColumns,
        ])
    ],
    [
        'label' => 'Отклоненные запросы (' . $dataRefusedProvider->count . ')',
        'content' => SearchFilter::widget([
                'model' => $refusedModel,
                'action' => ['contract'],
                'data' => GridviewHelper::prepareColumns(
                    $refusedModel::tableName(),
                    $filterColumns,
                    'refused',
                    'searchFilter',
                    null
                ),
                'role' => UserIdentity::ROLE_ORGANIZATION,
                'type' => 'refused',
                'customizable' => false
            ]) .
            GridView::widget([
            'dataProvider' => $dataRefusedProvider,
            'columns' => $preparedRefusedColumns,
        ])
    ],
];
?>

<div class="contracts">
    <p>
        <a href="<?= \yii\helpers\Url::to(['contract-list']) ?>" class="btn btn-success">Направить запрос на удаление
            договора</a>
    </p>
    <?= \yii\bootstrap\Tabs::widget([
        'items' => $items
    ]); ?>
</div>
