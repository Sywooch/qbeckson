<?php

use app\helpers\GridviewHelper;
use app\models\Contracts;
use app\models\Mun;
use app\models\Organization;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $activeContractsProvider \yii\data\ActiveDataProvider */
/* @var $confirmedContractsProvider \yii\data\ActiveDataProvider */
/* @var $pendingContractsProvider \yii\data\ActiveDataProvider */
/* @var $dissolvedContractsProvider \yii\data\ActiveDataProvider */
/* @var $endsContractsProvider \yii\data\ActiveDataProvider */
/* @var $searchActiveContracts \app\models\search\ContractsSearch */
/* @var $searchConfirmedContracts \app\models\search\ContractsSearch */
/* @var $searchPendingContracts \app\models\search\ContractsSearch */
/* @var $searchDissolvedContracts \app\models\search\ContractsSearch */
/* @var $searchEndsContracts \app\models\search\ContractsSearch */
$this->title = 'Договоры';
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
$programMunicipality = [
    'attribute' => 'programMunicipality',
    'value' => 'program.municipality.name',
    'label' => 'Муниципалитет',
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
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
    'format' => 'raw',
    'label' => 'Сертификат',
    'value' => function ($data) {
        return Html::a(
            $data->certificate->number,
            Url::to(['certificates/view', 'id' => $data->certificate->id]),
            ['target' => '_blank', 'data-pjax' => '0']
        );
    }
];
$programName = [
    'attribute' => 'programName',
    'label' => 'Программа',
    'format' => 'raw',
    'value' => function ($data) {
        return Html::a(
            $data->program->name,
            Url::to(['programs/view', 'id' => $data->program->id]),
            ['target' => '_blank', 'data-pjax' => '0']
        );
    },
];
$payerName = [
    'attribute' => 'payerName',
    'label' => 'Плательщик',
    'format' => 'raw',
    'value' => function ($data) {
        return Html::a(
            $data->payers->name,
            Url::to(['payers/view', 'id' => $data->payer->name]),
            ['target' => '_blank', 'data-pjax' => '0']
        );
    }
];
$status = [
    'attribute' => 'status',
    'value' => function ($data) {
        if ($data->status === 0) {
            return 'Заявка ожидает подтверждения';
        }
        if ($data->status === 3) {
            return 'Договор ожидает подписания';
        }
    },
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => Contracts::statuses(),
];
$date_termnate = [
    'attribute' => 'date_termnate',
    'format' => 'date',
];
$paid = [
    'attribute' => 'paid',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
];
$actions = [
    'class' => ActionColumn::class,
    'controller' => 'contracts',
    'template' => '{view}',
    'searchFilter' => false,
];

$all_parents_funds = [
    'attribute' => 'all_parents_funds',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => ['max' => 10000],
];

$activeColumns = $endsColumns = [
    [
        'attribute' => 'payer_id',
        'type' => SearchFilter::TYPE_HIDDEN
    ],
    $number,
    $date,
    $rezerv,
    $paid,
    $start_edu_contract,
    $stop_edu_contract,
    $group_id,
    $programMunicipality,
    $childFullName,
    $moduleName,
    $certificateNumber,
    $programName,
    $payerName,
    $all_parents_funds,
    $actions,
];
$confirmedColumns = [
    $certificateNumber,
    $childFullName,
    $moduleName,
    $programMunicipality,
    $programName,
    $payerName,
    $start_edu_contract,
    $stop_edu_contract,
    $all_parents_funds,
    [
        'class' => ActionColumn::class,
        'template' => '{dobr}',
        'buttons' => [
            'dobr' => function ($url, $model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-check"></span>',
                    Url::to(['contracts/verificate', 'id' => $model->id]),
                    ['title' => 'Ok']
                );
            },
        ],
        'searchFilter' => false,
    ],
];
$pendingColumns = [
    $certificateNumber,
    $programName,
    $payerName,
    $start_edu_contract,
    $stop_edu_contract,
    $status,
    $programMunicipality,
    $moduleName,
    $all_parents_funds,
    [
        'class' => ActionColumn::class,
        'template' => '{dobr}',
        'buttons' => [
            'dobr' => function ($url, $model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-check"></span>',
                    Url::to(['contracts/verificate', 'id' => $model->id]),
                    ['title' => 'Ok']
                );
            },
        ],
        'searchFilter' => false,
    ],
];
$dissolvedColumns = [
    $number,
    $date,
    $certificateNumber,
    $programName,
    $payerName,
    $moduleName,
    $date_termnate,
    $programMunicipality,
    $paid,
    $all_parents_funds,
    $actions,
];

$preparedActiveColumns = GridviewHelper::prepareColumns('contracts', $activeColumns, 'active');
$preparedConfirmedColumns = GridviewHelper::prepareColumns('contracts', $confirmedColumns, 'confirmed');
$preparedPendingColumns = GridviewHelper::prepareColumns('contracts', $pendingColumns, 'pending');
$preparedDissolvedColumns = GridviewHelper::prepareColumns('contracts', $dissolvedColumns, 'dissolved');
$preparedEndsColumns = GridviewHelper::prepareColumns('contracts', $endsColumns, 'ends');
?>
<ul class="nav nav-tabs">
    <li class="active">
        <a data-toggle="tab" href="#panel1">Действующие
            <span class="badge"><?= $activeContractsProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel2">Подтвержденные
            <span class="badge"><?= $confirmedContractsProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel3">Ожидающие подтверждения
            <span class="badge"><?= $pendingContractsProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel4">Заканчивающие действие
            <span class="badge"><?= $endsContractsProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel5">Расторгнутые
            <span class="badge"><?= $dissolvedContractsProvider->getTotalCount() ?></span>
        </a>
    </li>
</ul>
<br>
<div class="tab-content">
    <?php
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);

    $organizations = new Organization();
    $organization = $organizations->getOrganization();

    if ($roles['organizations'] and $organization['actual'] != 0) {
        echo "<p>";
        echo Html::a('Создать новый договор', ['contracts/create'], ['class' => 'btn btn-success']);
        echo "</p>";
    }
    ?>
    <div id="panel1" class="tab-pane fade in active">
        <?php if ($searchActiveContracts->payer_id) : ?>
            <p class="lead">Показаны результаты для программы: <?= $searchActiveContracts->programName; ?></p>
        <?php endif; ?>
        <?= SearchFilter::widget([
            'model' => $searchActiveContracts,
            'action' => ['personal/organization-contracts#panel1'],
            'data' => GridviewHelper::prepareColumns(
                'contracts',
                $activeColumns,
                'active',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'active'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $activeContractsProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                if ($model->wait_termnate > 0) {
                    return ['class' => 'danger'];
                } elseif ($model->wait_termnate < 1 && in_array($model->status, [Contracts::STATUS_ACTIVE, Contracts::STATUS_CREATED, Contracts::STATUS_ACCEPTED]) && $model->all_parents_funds > 0) {
                    return ['class' => 'warning'];
                }
            },
            'columns' => $preparedActiveColumns,
        ]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchConfirmedContracts,
            'action' => ['personal/organization-contracts#panel2'],
            'data' => GridviewHelper::prepareColumns(
                'contracts',
                $confirmedColumns,
                'confirmed',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'confirmed'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $confirmedContractsProvider,
            'filterModel' => null,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                if ($model->wait_termnate === 1) {
                    return ['class' => 'danger'];
                } elseif ($model->wait_termnate < 1 && in_array($model->status, [Contracts::STATUS_ACTIVE, Contracts::STATUS_CREATED, Contracts::STATUS_ACCEPTED]) && $model->all_parents_funds > 0) {
                    return ['class' => 'warning'];
                }
            },
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedConfirmedColumns,
        ]); ?>
    </div>
    <div id="panel3" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchPendingContracts,
            'action' => ['personal/organization-contracts#panel3'],
            'data' => GridviewHelper::prepareColumns(
                'contracts',
                $pendingColumns,
                'pending',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'pending'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $pendingContractsProvider,
            'filterModel'  => null,
            'pjax'         => true,
            'rowOptions'   => function ($model, $index, $widget, $grid)
            {
                if ($model->wait_termnate === 1) {
                    return ['class' => 'danger'];
                } elseif ($model->wait_termnate < 1 && in_array($model->status, [Contracts::STATUS_ACTIVE, Contracts::STATUS_CREATED, Contracts::STATUS_ACCEPTED]) && $model->all_parents_funds > 0) {
                    return ['class' => 'warning'];
                }
            },
            'summary'      => false,
            'columns'      => $preparedPendingColumns,
        ]); ?>
    </div>
    <div id="panel5" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchDissolvedContracts,
            'action' => ['personal/organization-contracts#panel5'],
            'data' => GridviewHelper::prepareColumns(
                'contracts',
                $dissolvedColumns,
                'dissolved',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'dissolved'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $dissolvedContractsProvider,
            'filterModel' => null,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                if ($model->wait_termnate === 1) {
                    return ['class' => 'danger'];
                }
            },
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedDissolvedColumns,
        ]); ?>
    </div>
    <div id="panel4" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchEndsContracts,
            'action' => ['personal/operator-contracts#panel4'],
            'data' => GridviewHelper::prepareColumns(
                'contracts',
                $endsColumns,
                'ends',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'ends'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $endsContractsProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedEndsColumns,
        ]); ?>
    </div>
</div>
<?= \app\widgets\Export::widget([
    'dataProvider' => $searchContractsall->search(Yii::$app->request->queryParams, 999999),
    'columns' => GridviewHelper::prepareColumns('contracts', $activeColumns, 'active', 'export'),
    'group' => 'organization-contracts',
    'table' => 'contracts',
]); ?>
