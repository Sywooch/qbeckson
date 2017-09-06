<?php

use app\helpers\GridviewHelper;
use app\models\Mun;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchRequest \app\models\search\OrganizationSearch */
/* @var $searchRegistry \app\models\search\OrganizationSearch */
/* @var $registryProvider \yii\data\ActiveDataProvider */
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
        $programsCount = $model->getPrograms()->andWhere(['programs.verification' => 2])->count();
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
        $childrenCount = count(array_unique(
            $model->getChildren()->andWhere(['contracts.status' => 1])->asArray()->all()
        ));
        return $childrenCount > 0 ? $childrenCount : '-';
    },
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 10000
    ]
];
$amount_child = [
    'attribute' => 'amount_child',
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
$readonlyColumns = [
    $name,
    $fio_contact,
    $site,
    $email,
    $mun,
    $type,
];

$preparedRegistryColumns = GridviewHelper::prepareColumns('organization', $registryColumns, 'register');
$preparedRequestColumns = GridviewHelper::prepareColumns('organization', $requestColumns, 'request');
$preparedReadonlyColumns = GridviewHelper::prepareColumns('organization', $readonlyColumns, 'request');
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
        <a data-toggle="tab" href="#panel2">Подтверждённые
            <span class="badge"><?= $confirmProvider->totalCount ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel4">Отклонены
            <span class="badge"><?= $rejectProvider->totalCount ?></span>
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
        <p class="lead">Экспорт данных:</p>
        <?= ExportMenu::widget([
            'dataProvider' => $allRegistryProvider,
            'exportConfig' => [
                ExportMenu::FORMAT_EXCEL => false,
            ],
            'target' => ExportMenu::TARGET_BLANK,
            'columns' => GridviewHelper::prepareExportColumns($registryColumns),
            'showColumnSelector' => false
        ]); ?>
        <br>
        <br>
        <p class=""><strong><span class="warning">*</span> Загрузка начнётся в новом окне и может занять некоторое время.</strong></p>
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
    <div id="panel2" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchConfirm,
            'action' => ['personal/payer-organizations#panel2'],
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
            'dataProvider' => $confirmProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedReadonlyColumns
        ]); ?>
    </div>
    <div id="panel4" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchReject,
            'action' => ['personal/payer-organizations#panel4'],
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
            'dataProvider' => $rejectProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedReadonlyColumns
        ]); ?>
    </div>
</div>
