<?php

use app\helpers\GridviewHelper;
use app\models\Mun;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use kartik\grid\GridView;
use yii\bootstrap\Tabs;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchCertificates \app\models\search\CertificatesSearch */
/* @var $certificatesProviderPf \yii\data\ActiveDataProvider */
/* @var $certificatesProviderAccounting \yii\data\ActiveDataProvider */
/* @var $allCertificatesProvider \yii\data\ActiveDataProvider */

$this->title = 'Сертификаты';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$columns = [
    [
        'attribute' => 'payer_id',
        'type' => SearchFilter::TYPE_HIDDEN
    ],
    [
        'attribute' => 'payerMunicipality',
        'value' => function ($model) {
            /** @var \app\models\Certificates $model */
            return Html::a(
                $model->payer->municipality->name,
                ['mun/view', 'id' => $model->payer->municipality->id],
                ['target' => '_blank', 'data-pjax' => '0']
            );
        },
        'label' => 'Муниципалитет',
        'format' => 'raw',
        'type' => SearchFilter::TYPE_DROPDOWN,
        'data' => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
    ],
    [
        'attribute' => 'number',
        'label' => 'Номер',
    ],
    [
        'attribute' => 'payer',
        'value' => function ($model) {
            return Html::a(
                $model->payer->name,
                Url::to(['payers/view', 'id' => $model->payer->id]),
                ['target' => '_blank', 'data-pjax' => '0']
            );
        },
        'format' => 'raw'
    ],
    [
        'attribute' => 'soname',
    ],
    [
        'attribute' => 'name',
    ],
    [
        'attribute' => 'phname',
    ],
    [
        'attribute' => 'nominal',
        'type' => SearchFilter::TYPE_RANGE_SLIDER,
    ],
    [
        'attribute' => 'rezerv',
        'label' => 'Резерв',
        'type' => SearchFilter::TYPE_RANGE_SLIDER,
        'value' => function ($data) {
            return abs(round($data->rezerv));
        },
    ],
    [
        'attribute' => 'balance',
        'label' => 'Остаток',
        'type' => SearchFilter::TYPE_RANGE_SLIDER,
        'value' => function ($data) {
            return round($data->balance);
        },
    ],
    [
        'attribute' => 'contractCount',
        'label' => 'Договоров',
        'type' => SearchFilter::TYPE_TOUCH_SPIN,
    ],
    [
        'class' => ActionColumn::class,
        'controller' => 'certificates',
        'template' => '{view}',
        'searchFilter' => false,
    ],
];
?>
<?php if ($searchCertificates->payer_id) : ?>
    <p class="lead">Показаны результаты для плательщика: <?= $searchCertificates->payer; ?></p>
<?php endif; ?>
<?= SearchFilter::widget([
    'model' => $searchCertificates,
    'action' => ['personal/operator-certificates'],
    'data' => GridviewHelper::prepareColumns(
        'certificates',
        $columns,
        null,
        'searchFilter',
        null
    ),
    'role' => UserIdentity::ROLE_OPERATOR,
]); ?>
<?php
$preparedColumns = GridviewHelper::prepareColumns('certificates', $columns);
$items = [
    [
        'label' => 'Сертификаты ПФ',
        'content' => GridView::widget([
            'dataProvider' => $certificatesProviderPf,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedColumns,
        ]),
        'active' => true
    ],
    [
        'label' => 'Сертификаты учета',
        'content' => GridView::widget([
            'dataProvider' => $certificatesProviderAccounting,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedColumns,
        ])
    ],

];

echo Tabs::widget([
    'items' => $items
]);
?>
<?= \app\widgets\Export::widget([
    'dataProvider' => $allCertificatesProvider,
    'columns' => GridviewHelper::prepareColumns('certificates', $columns, null, 'export'),
    'group' => 'operator-certificates',
    'table' => 'certificates',
]); ?>
