<?php
use app\helpers\GridviewHelper;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\grid\ActionColumn;
use app\helpers\AppHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Счета';
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $searchInvoices \app\models\search\InvoicesSearch */
/* @var $invoicesProvider \yii\data\ActiveDataProvider */

$columns = [
    [
        'attribute' => 'number'
    ],
    [
        'attribute' => 'date'
    ],
    [
        'attribute' => 'month',
        'label' => 'Месяц',
        'value' => function ($model) {
            /** @var \app\models\Invoices $model */
            return AppHelper::getMonthName($model->month);
        },
        'type' => SearchFilter::TYPE_DROPDOWN,
        'data' => AppHelper::monthes(),
    ],
    [
        'attribute' => 'organization',
        'label' => 'Организация',
        'format' => 'raw',
        'value' => function ($model) {
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
        'label' => 'Тип',
        'format' => 'raw',
        'value' => function ($model) {
            /** @var \app\models\Invoices $model */
            return $model->prepayment === 1 ? 'Аванс' : 'Счёт';
        },
        'type' => SearchFilter::TYPE_DROPDOWN,
        'data' => [
            0 => 'Счёт',
            1 => 'Аванс',
        ],
    ],
    [
        'attribute' => 'status',
        'format' => 'raw',
        'value' => function ($model) {
            /** @var \app\models\Invoices $model */
            return $model::statuses()[$model->status];
        },
        'type' => SearchFilter::TYPE_DROPDOWN,
        'data' => $searchInvoices::statuses(),
    ],
    [
        'attribute' => 'sum',
        'type' => SearchFilter::TYPE_RANGE_SLIDER,
        'pluginOptions' => [
            'max' => 10000000
        ]
    ],
    [
        'attribute' => 'link',
        'label' => 'Скачать',
        'format' => 'raw',
        'value' => function ($model) {
            /** @var \app\models\Invoices $model */
            if ($model->prepayment === 1) {
                return Html::a(
                    '<span class="glyphicon glyphicon-download-alt"></span>',
                    Url::to(['invoices/mpdf', 'id' => $model->id])
                );
            }

            return Html::a(
                '<span class="glyphicon glyphicon-download-alt"></span>',
                Url::to(['invoices/invoice', 'id' => $model->id])
            );
        }
    ],
    [
        'class' => ActionColumn::class,
        'controller' => 'invoices',
        'template' => '{view}',
        'searchFilter' => false,
    ],
];

$preparedColumns = GridviewHelper::prepareColumns('invoices', $columns);
?>
<?php if ($searchInvoices->organization_id && $searchInvoices->organization) : ?>
    <p class="lead">Показаны результаты для организации: <?= $searchInvoices->organization; ?></p>
<?php endif; ?>
<?php
echo SearchFilter::widget([
    'model' => $searchInvoices,
    'action' => ['personal/payer-invoices'],
    'data' => GridviewHelper::prepareColumns(
        'invoices',
        $columns,
        null,
        'searchFilter',
        null
    ),
    'role' => UserIdentity::ROLE_PAYER,
]);

echo GridView::widget([
    'dataProvider' => $invoicesProvider,
    'filterModel' => null,
    'columns' => $preparedColumns
]);
