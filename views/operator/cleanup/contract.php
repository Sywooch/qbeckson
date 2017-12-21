<?php

use kartik\grid\GridView;
use app\widgets\SearchFilter;
use app\helpers\GridviewHelper;
use app\models\UserIdentity;
use app\models\ContractDeleteApplication;
use app\helpers\ArrayHelper;

/** @var $this yii\web\View */
/** @var $modelForm \app\models\forms\ContractRemoveForm */
/** @var $waitingModel \app\models\search\ContractDeleteApplicationSearch */
/** @var $confirmedModel \app\models\search\ContractDeleteApplicationSearch */
/** @var $refusedModel \app\models\search\ContractDeleteApplicationSearch */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $dataConfirmedProvider \yii\data\ActiveDataProvider */
/** @var $dataRefusedProvider \yii\data\ActiveDataProvider */

$this->title = 'Список запросов на удаление';
$this->params['breadcrumbs'][] = $this->title;

\app\assets\operatorDeleteContractAsset\OperatorDeleteContractAsset::register($this);

$waitingColumns = $confirmedColumns = $refusedColumns = [
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
        'format' => 'url',
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
$refusedColumns[] = ['attribute' => 'confirmed_at', 'format' => 'datetime'];
$confirmedColumns[] = ['attribute' => 'confirmed_at', 'format' => 'datetime'];
$refusedColumns[] = ['attribute' => 'reason'];
$confirmedColumns[] = ['attribute' => 'reason'];
$waitingColumns[] = ['attribute' => 'reason'];
$waitingColumns[] = [
    'label' => 'Решение',
    'format' => 'raw',
    'value' => function ($application) {
        return $this->render('_confirm_button', ['model' => $application]);
    }
];

$filterColumns = [['attribute' => 'contractNumber']];


$preparedWaitingColumns = GridviewHelper::prepareColumns($waitingModel::tableName(), $waitingColumns, 'waiting');
$preparedConfirmedColumns = GridviewHelper::prepareColumns($confirmedModel::tableName(), $confirmedColumns,
    'confirmed');
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
                'type' => 'waiting'
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
                    $filterColumns,
                    'confirmed',
                    'searchFilter',
                    null
                ),
                'role' => UserIdentity::ROLE_ORGANIZATION,
                'type' => 'confirmed'
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
                'type' => 'refused'
            ]) .
            GridView::widget([
                'dataProvider' => $dataRefusedProvider,
                'columns' => $preparedRefusedColumns,
            ])
    ],
];
?>

<div class="contracts">
    <?= \yii\bootstrap\Tabs::widget([
        'items' => $items
    ]); ?>
</div>

<?= $this->render('_form_modal', ['model' => $modelForm,]) ?>
