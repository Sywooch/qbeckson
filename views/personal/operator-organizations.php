<?php

use app\helpers\GridviewHelper;
use app\models\Certificates;
use app\models\Mun;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchRequest \app\models\search\OrganizationSearch */
/* @var $searchRegistry \app\models\search\OrganizationSearch */
/* @var $registryProvider \yii\data\ActiveDataProvider */
/* @var $searchRefused\app\models\search\OrganizationSearch */
/* @var $refusedProvider \yii\data\ActiveDataProvider */
/* @var $allRegistryProvider \yii\data\ActiveDataProvider */
/* @var $requestProvider \yii\data\ActiveDataProvider */
$this->title = 'Поставщики образовательных услуг';
$this->params['breadcrumbs'][] = $this->title;

$name = [
    'attribute' => 'name',
];
$fio_contact = [
    'attribute' => 'fio_contact',
];
$cratedate = [
    'attribute' => 'cratedate',
];
$site = [
    'attribute' => 'site',
    'format' => 'url'
];
$phone = [
    'attribute' => 'phone',
];
$max_child = [
    'attribute' => 'max_child',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 10000
    ]
];
$raiting = [
    'attribute' => 'raiting',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 100
    ]
];
$type = [
    'attribute' => 'type',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        return $model::types()[$model->type];
    },
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => $searchRegistry::types(),
];
$mun = [
    'attribute' => 'mun',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        return Html::a(
            $model->municipality->name,
            ['mun/view', 'id' => $model->municipality->id],
            ['target' => '_blank', 'data-pjax' => '0']
        );
    },
    'format' => 'raw',
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
];
$programs = [
    'attribute' => 'programs',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        $programsCount = count(array_filter($model->programs, function ($val) { return $val->verification === \app\models\Programs::VERIFICATION_DONE; }));
        return (int)$programsCount > 0 ? $programsCount : '-';
    },
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 1000
    ]
];
$children = [
    'attribute' => 'children',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        $childrenCount = $model->getChildren()->select('certificates.id')->distinct()->leftJoin(Certificates::tableName(), 'certificates.id = contracts.certificate_id')->andWhere(['contracts.status' => 1])->count();
        return $childrenCount > 0 ? $childrenCount : '-';
    },
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 10000
    ]
];
$amount_child = [
    'attribute' => 'amount_child',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        $childrenCount = $model->getChildren()->andWhere(['contracts.status' => 1])->count();
        return $childrenCount > 0 ? $childrenCount : '-';
    },
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 10000
    ]
];
$actual = [
    'attribute' => 'actual',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        return $model->actual === 0 ? '-' : '+';
    },
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => [
        1 => 'Да',
        0 => 'Нет'
    ]
];
$email = [
    'attribute' => 'email',
];
$actions = [
    'class' => ActionColumn::class,
    'controller' => 'organization',
    'template' => '{view}',
    'searchFilter' => false,
];

$registryColumns = [
    $name,
    $fio_contact,
    $cratedate,
    $site,
    $phone,
    $max_child,
    $raiting,
    $type,
    $mun,
    $programs,
    $children,
    $amount_child,
    $actual,
    $email,
    $actions,
];

$refusedColumns = [
    $name,
    $fio_contact,
    $cratedate,
    $site,
    $phone,
    $max_child,
    $raiting,
    $type,
    $mun,
    $programs,
    $children,
    $amount_child,
    $actual,
    $actions,
];
$requestColumns = [
    $name,
    $fio_contact,
    $site,
    $email,
    $mun,
    $type,
    $actions,
];

$preparedRegistryColumns = GridviewHelper::prepareColumns('organization', $registryColumns, 'register');
$preparedRequestColumns = GridviewHelper::prepareColumns('organization', $requestColumns, 'request');
?>
<ul class="nav nav-tabs">
    <li class="active">
        <a data-toggle="tab" href="#panel-registry">Реестр
            <span class="badge"><?= $registryProvider->totalCount ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel-requests">Заявки
            <span class="badge"><?= $requestProvider->totalCount ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel-refused">Отказы
            <span class="badge"><?= $refusedProvider->totalCount ?></span>
        </a>
    </li>
</ul>
<br>
<div class="tab-content">
    <div id="panel-registry" class="tab-pane fade in active">
        <?= SearchFilter::widget([
            'model' => $searchRegistry,
            'action' => ['personal/operator-organizations#panel-registry'],
            'data' => GridviewHelper::prepareColumns(
                'organization',
                $registryColumns,
                'register',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'register'
        ]); ?>
        <?= Html::a(
            'Добавить поставщика образовательных услуг',
            ['organization/create'],
            ['class' => 'btn btn-success']
        ) ?>
        <div class="pull-right">
            <?= Html::a('Пересчитать лимиты', ['organization/alllimit'], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Пересчитать рейтинги', ['organization/allraiting'], ['class' => 'btn btn-primary']) ?>
        </div><br><br>
        <?= GridView::widget([
            'dataProvider' => $registryProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedRegistryColumns,
        ]); ?>
        <?= \app\widgets\Export::widget([
            'dataProvider' => $allRegistryProvider,
            'columns' => GridviewHelper::prepareColumns('organization', $registryColumns, 'register', 'export'),
            'group' => 'operator-organizations',
            'table' => 'organization',
        ]); ?>
    </div>
    <div id="panel-requests" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchRequest,
            'action' => ['personal/operator-organizations#panel-requests'],
            'data' => GridviewHelper::prepareColumns(
                'organization',
                $requestColumns,
                'request',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'request'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $requestProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => GridviewHelper::prepareColumns(
                'organization',
                $requestColumns,
                'request'
            )
        ]); ?>
    </div>
    <div id="panel-refused" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model'  => $searchRefused,
            'action' => ['personal/operator-organizations#panel-refused'],
            'data'   => GridviewHelper::prepareColumns(
                'organization',
                $refusedColumns,
                'request',
                'searchFilter',
                null
            ),
            'role'   => UserIdentity::ROLE_OPERATOR,
            'type'   => 'request'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $refusedProvider,
            'filterModel'  => null,
            'pjax'         => true,
            'summary'      => false,
            'columns'      => GridviewHelper::prepareColumns(
                'organization',
                $refusedColumns,
                'request'
            )
        ]); ?>
    </div>
</div>
