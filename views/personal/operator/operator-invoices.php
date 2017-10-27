<?php

use app\helpers\AppHelper;
use app\helpers\GridviewHelper;
use app\models\Payers;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Счета';
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $exposedSearchInvoices \app\models\search\InvoicesSearch */
/* @var $exposedInvoicesProvider yii\data\ActiveDataProvider */
/* @var $paidSearchInvoices \app\models\search\InvoicesSearch */
/* @var $paidInvoicesProvider yii\data\ActiveDataProvider */
/* @var $removedSearchInvoices \app\models\search\InvoicesSearch */
/* @var $removedInvoicesProvider yii\data\ActiveDataProvider */
/* @var $munList array */

$columns = [
    [
        'attribute' => 'number'
    ],
    [
        'attribute' => 'date'
    ],
    [
        'attribute' => 'month',
        'label'     => 'Месяц',
        'value'     => function ($model)
        {
            /** @var \app\models\Invoices $model */
            return AppHelper::getMonthName($model->month);
        },
        'type'      => SearchFilter::TYPE_DROPDOWN,
        'data'      => AppHelper::monthes(),
    ],
    [
        'attribute' => 'organization',
        'label'     => 'Организация',
        'format'    => 'raw',
        'value'     => function ($model)
        {
            /** @var \app\models\Invoices $model */
            return Html::a(
                $model->organization->name,
                Url::to(['organization/view', 'id' => $model->organization->id]),
                ['target' => '_blank', 'data-pjax' => '0']
            );
        },
    ],
    [
        'attribute' => 'prepayment',
        'label'     => 'Тип',
        'format'    => 'raw',
        'value'     => function ($model)
        {
            /** @var \app\models\Invoices $model */
            return $model->prepayment === 1 ? 'Аванс' : 'Счёт';
        },
        'type'      => SearchFilter::TYPE_DROPDOWN,
        'data'      => [
            0 => 'Счёт',
            1 => 'Аванс',
        ],
    ],
    [
        'attribute' => 'status',
        'format'    => 'raw',
        'value'     => function ($model)
        {
            /** @var \app\models\Invoices $model */
            return $model::statuses()[$model->status];
        },
        'type'      => SearchFilter::TYPE_DROPDOWN,
        'data'      => $exposedSearchInvoices::statuses(),
    ],
    [
        'attribute'     => 'sum',
        'type'          => SearchFilter::TYPE_RANGE_SLIDER,
        'pluginOptions' => [
            'max' => 10000000
        ]
    ],
    [
        'attribute'    => 'link',
        'label'        => 'Скачать',
        'format'       => 'raw',
        'value'        => function ($model)
        {
            /** @var \app\models\Invoices $model */
            if ($model->prepayment === 1) {
                return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', !empty($model->pdf) ? Url::to($model->pdf) : Url::to(['invoices/mpdf', 'id' => $model->id]));
            }

            return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', !empty($model->pdf) ? Url::to($model->pdf) : Url::to(['invoices/invoice', 'id' => $model->id]));
        },
        'searchFilter' => false,
    ],
    ['attribute' => 'payer',
     'value'     => function ($model)
     {
         return Html::a($model->payer->name, Url::to(['payers/view', 'id' => $model->payer->id]));
     },
     'format'    => 'raw',
     'type'      => SearchFilter::TYPE_INPUT,
     'data'      => ArrayHelper::map(Payers::find()->all(), 'id', 'name'),
    ],
    [
        'class'        => ActionColumn::class,
        'controller'   => 'invoices',
        'template'     => '{view}',
        'searchFilter' => false,
    ],
    [
        'attribute' => 'mun',
        'type'      => SearchFilter::TYPE_DROPDOWN,
        'data'      => $munList,
        'visible'   => false
    ],
];

$preparedColumns = GridviewHelper::prepareColumns('invoices', $columns);
?>

<ul class="nav nav-tabs">
    <li class="active">
        <a data-toggle="tab" href="#panel1">Выставленные
            <span class="badge"><?= $exposedInvoicesProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel2">Оплаченные
            <span class="badge"><?= $paidInvoicesProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel3">Удаленные
            <span class="badge"><?= $removedInvoicesProvider->getTotalCount() ?></span>
        </a>
    </li>
</ul>

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <?php if ($exposedSearchInvoices->organization_id && $exposedSearchInvoices->organization) : ?>
            <p class="lead">Показаны результаты для организации: <?= $exposedSearchInvoices->organization; ?></p>
        <?php endif; ?>

        <?php echo SearchFilter::widget([
            'model' => $exposedSearchInvoices,
            'action' => ['personal/operator-invoices'],
            'data' => GridviewHelper::prepareColumns(
                'invoices',
                $columns,
                null,
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_OPERATOR,
        ]);

        echo GridView::widget([
            'dataProvider' => $exposedInvoicesProvider,
            'filterModel' => null,
            'columns' => $preparedColumns
        ]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?php if ($paidSearchInvoices->organization_id && $paidSearchInvoices->organization) : ?>
            <p class="lead">Показаны результаты для организации: <?= $paidSearchInvoices->organization; ?></p>
        <?php endif; ?>

        <?php echo SearchFilter::widget([
            'model' => $paidSearchInvoices,
            'action' => ['personal/operator-invoices'],
            'data' => GridviewHelper::prepareColumns(
                'invoices',
                $columns,
                null,
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_OPERATOR,
        ]);

        echo GridView::widget([
            'dataProvider' => $paidInvoicesProvider,
            'filterModel' => null,
            'columns' => $preparedColumns
        ]); ?>
    </div>
    <div id="panel3" class="tab-pane fade">
        <?php if ($removedSearchInvoices->organization_id && $removedSearchInvoices->organization) : ?>
            <p class="lead">Показаны результаты для организации: <?= $removedSearchInvoices->organization; ?></p>
        <?php endif; ?>

        <?php echo SearchFilter::widget([
            'model' => $removedSearchInvoices,
            'action' => ['personal/operator-invoices'],
            'data' => GridviewHelper::prepareColumns(
                'invoices',
                $columns,
                null,
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_OPERATOR,
        ]);

        echo GridView::widget([
            'dataProvider' => $removedInvoicesProvider,
            'filterModel' => null,
            'columns' => $preparedColumns
        ]); ?>
    </div>
</div>
