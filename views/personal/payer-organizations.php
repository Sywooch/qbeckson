<?php
use app\helpers\GridviewHelper;
use app\models\Mun;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;

$this->title = 'Организации';
$this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */
/* @var $searchRequest \app\models\search\OrganizationSearch */
/* @var $searchReject \app\models\search\OrganizationSearch */
/* @var $searchRegistry \app\models\search\OrganizationSearch */
/* @var $searchConfirm \app\models\search\OrganizationSearch */
/* @var $registryProvider \yii\data\ActiveDataProvider */
/* @var $requestProvider \yii\data\ActiveDataProvider */
/* @var $confirmProvider \yii\data\ActiveDataProvider */
/* @var $rejectProvider \yii\data\ActiveDataProvider */

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
    'value' => 'municipality.name',
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
];
$programs = [
    'attribute' => 'programs',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        return $model->getPrograms()->andWhere(['programs.verification' => 2])->count();
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
        return count(array_unique(ArrayHelper::toArray(
            $model->getChildren()->andWhere(['contracts.status' => 1])->all()
        )));
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

$preparedRegistryColumns = GridviewHelper::prepareColumns('organization', $registryColumns, 'register');
$preparedRequestColumns = GridviewHelper::prepareColumns('organization', $requestColumns, 'request');
?>
<ul class="nav nav-tabs">
    <li class="active">
        <a data-toggle="tab" href="#panel1">Реестр
            <span class="badge"><?= $registryProvider->totalCount ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel2">Подтверждённые
            <span class="badge"><?= $confirmProvider->totalCount ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel3">Заявки
            <span class="badge"><?= $requestProvider->totalCount ?></span>
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
    <div id="panel1" class="tab-pane fade in active">
        <?= SearchFilter::widget([
            'model' => $searchRegistry,
            'action' => ['personal/payer-organizations'],
            'data' => GridviewHelper::prepareColumns(
                'organization',
                $registryColumns,
                'register',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'register'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $registryProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedRegistryColumns,
        ]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchConfirm,
            'action' => ['personal/payer-organizations'],
            'data' => GridviewHelper::prepareColumns(
                'organization',
                $requestColumns,
                'request',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'request'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $confirmProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedRequestColumns
        ]); ?>
    </div>
    <div id="panel3" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchRequest,
            'action' => ['personal/payer-organizations'],
            'data' => GridviewHelper::prepareColumns(
                'organization',
                $requestColumns,
                'request',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'request'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $requestProvider,
            'rowOptions' => function ($model) {
                /** @var \app\models\Cooperate $cooperate */
                $cooperate = \app\models\Cooperate::findOne([
                    'organization_id' => $model->id,
                    'payer_id' => Yii::$app->user->identity->payer->id
                ]);
                if (null !== $cooperate->appeal_reason) {
                    return ['class' => 'danger'];
                }
            },
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedRequestColumns
        ]); ?>
    </div>
    <div id="panel4" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchReject,
            'action' => ['personal/payer-organizations'],
            'data' => GridviewHelper::prepareColumns(
                'organization',
                $requestColumns,
                'request',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'request'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $rejectProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedRequestColumns
        ]); ?>
    </div>
</div>
