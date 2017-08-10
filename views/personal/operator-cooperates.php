<?php

use yii\grid\ActionColumn;
use app\helpers\GridviewHelper;
use app\models\Mun;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchAppealed app\models\search\CooperateSearch */
/* @var $searchActive app\models\search\CooperateSearch */
/* @var $appealedProvider yii\data\ActiveDataProvider */
/* @var $activeProvider yii\data\ActiveDataProvider */

$this->title = 'Соглашения';
$this->params['breadcrumbs'][] = $this->title;

$number = [
    'attribute' => 'number',
];
$date = [
    'attribute' => 'date',
    'format' => 'date',
    'label' => 'Дата соглашения',
];
$organizationName = [
    'attribute' => 'organizationName',
    'label' => 'Организация',
    'format' => 'raw',
    'value' => function ($model) {
        /** @var \app\models\Cooperate $model */
        return Html::a(
            $model->organization->name,
            ['organization/view', 'id' => $model->organization->id],
            ['class' => 'blue', 'target' => '_blank']
        );
    },
];
$payerName = [
    'attribute' => 'payerName',
    'label' => 'Плательщик',
    'format' => 'raw',
    'value' => function ($model) {
        /** @var \app\models\Cooperate $model */
        return Html::a(
            $model->payer->name,
            ['payers/view', 'id' => $model->payer->id],
            ['class' => 'blue', 'target' => '_blank']
        );
    },
];
$payerMunicipality = [
    'attribute' => 'payerMunicipality',
    'label' => 'Муниципалитет',
    'format' => 'raw',
    'data' => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
    'type' => SearchFilter::TYPE_DROPDOWN,
    'value' => function ($model) {
        /** @var \app\models\Cooperate $model */
        return Html::a(
            $model->payer->municipality->name,
            ['mun/view', 'id' => $model->payer->municipality->id],
            ['class' => 'blue', 'target' => '_blank']
        );
    },
];
$contractsCount = [
    'label' => 'Число договоров',
    'attribute' => 'contractsCount',
    'format'=> 'raw',
    'value' => function ($data) {
        $contracts = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['payer_id' => $data->payers->id])
            ->andWhere(['organization_id' => $data->organization->id])
            ->count();

        return Html::a(
            $contracts,
            [
                'personal/operator-contracts',
                'SearchActiveContracts[organizationName]' => $data->organization->name,
                'SearchActiveContracts[organization_id]' => $data->organization->id,
                'SearchActiveContracts[payerName]' => $data->payers->name,
                'SearchActiveContracts[payer_id]' => $data->payers->id,
            ],
            ['class' => 'blue', 'target' => '_blank', 'data-pjax' => '0']
        );
    },
];
$payer_id = [
    'attribute' => 'payer_id',
    'type' => SearchFilter::TYPE_HIDDEN,
];
$actions = [
    'class' => ActionColumn::class,
    'controller' => 'cooperate',
    'template' => '{view}',
    'searchFilter' => false
];
$reject_reason = [
    'attribute' => 'reject_reason'
];
$appeal_reason = [
    'attribute' => 'appeal_reason'
];
$created_date = [
    'attribute' => 'created_date'
];

$columns = [
    $number,
    $date,
    $organizationName,
    $payerName,
    $payerMunicipality,
    $contractsCount,
    $payer_id,
    $actions,
]

?>
<div class="cooperate-index">
    <ul class="nav nav-tabs">
        <li class="active">
            <a data-toggle="tab" href="#panel1">Действующие
                <span class="badge"><?= $activeProvider->getTotalCount() ?></span>
            </a>
        </li>
        <li>
            <a data-toggle="tab" href="#panel2">Отказы
                <span class="badge"><?= $appealedProvider->getTotalCount() ?></span>
            </a>
        </li>
    </ul>
    <br><br>
    <div class="tab-content">
        <div id="panel1" class="tab-pane fade in active">
            <?php if ($searchActive->payer_id) : ?>
                <p class="lead">Показаны результаты для плательщика: <?= $searchActive->payerName; ?></p>
            <?php endif; ?>
            <?= SearchFilter::widget([
                'model' => $searchActive,
                'action' => ['personal/operator-cooperates#panel1'],
                'data' => GridviewHelper::prepareColumns(
                    'cooperate',
                    $columns,
                    null,
                    'searchFilter',
                    null
                ),
                'role' => UserIdentity::ROLE_OPERATOR,
            ]); ?>
            <?= GridView::widget([
                'dataProvider' => $activeProvider,
                'filterModel' => null,
                'columns' => GridviewHelper::prepareColumns('cooperate', $columns),
            ]); ?>
        </div>
        <div id="panel2" class="tab-pane fade">
            <?= GridView::widget([
                'dataProvider' => $appealedProvider,
                'filterModel' => $searchAppealed,
                'columns' => GridviewHelper::prepareColumns('cooperate', $columns),
            ]); ?>
        </div>
    </div>
</div>
