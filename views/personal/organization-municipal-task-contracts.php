<?php

use app\helpers\GridviewHelper;
use app\models\UserIdentity;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $activeContractsProvider \yii\data\ActiveDataProvider */
/* @var $pendingContractsProvider \yii\data\ActiveDataProvider */
/* @var $searchActiveContracts \app\models\search\MunicipalTaskContractSearch */
/* @var $searchPendingContracts \app\models\search\MunicipalTaskContractSearch */
$this->title = 'Муниципальные задания';
$this->params['breadcrumbs'][] = $this->title;

$number = [
    'attribute' => 'id',
];
$group_id = [
    'attribute' => 'group_id',
    'value' => 'group.name',
];
$childFullName = [
    'attribute' => 'childFullName',
    'value' => 'certificate.fio_child',
    'label' => 'ФИО ребёнка'
];
$moduleName = [
    'attribute' => 'moduleName',
    'value' => 'group.year.fullname',
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
            $data->payer->name,
            Url::to(['payers/view', 'id' => $data->payer->id]),
            ['target' => '_blank', 'data-pjax' => '0']
        );
    }
];
$actions = [
    'class' => ActionColumn::class,
    'controller' => 'contracts',
    'template' => '{view}',
        'buttons' => [
            'view' => function ($url, $model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-eye-open"></span>',
                    Url::to(['municipal-task-contract/view', 'id' => $model->id])
                );
            },
        ],
    'searchFilter' => false,
];

$activeColumns = [
    $number,
    $group_id,
    $childFullName,
    $moduleName,
    $certificateNumber,
    $programName,
    $payerName,
    $actions,
];
$pendingColumns = [
    $certificateNumber,
    $programName,
    $payerName,
    $moduleName,
    [
        'class' => ActionColumn::class,
        'template' => '{approve}',
        'buttons' => [
            'approve' => function ($url, $model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-check"></span>',
                    Url::to(['municipal-task-contract/approve', 'id' => $model->id]),
                    ['title' => 'Ok']
                );
            },
        ],
        'searchFilter' => false,
    ],
];

$preparedActiveColumns = GridviewHelper::prepareColumns('municipal_task_contract', $activeColumns, 'active');
$preparedPendingColumns = GridviewHelper::prepareColumns('municipal_task_contract', $pendingColumns, 'pending');
?>
<ul class="nav nav-tabs">
    <li class="active">
        <a data-toggle="tab" href="#panel1">Действующие
            <span class="badge"><?= $activeContractsProvider->getTotalCount() ?></span>
        </a>
    </li>
     <li>
        <a data-toggle="tab" href="#panel3">Ожидающие подтверждения
            <span class="badge"><?= $pendingContractsProvider->getTotalCount() ?></span>
        </a>
    </li>
</ul>
<br>
<div class="tab-content">
    <?php
    /*$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);

    $organizations = new Organization();
    $organization = $organizations->getOrganization();

    if ($roles['organizations'] and $organization['actual'] != 0) {
        echo "<p>";
        echo Html::a('Создать новый договор', ['contracts/create'], ['class' => 'btn btn-success']);
        echo "</p>";
    }*/
    ?>
    <div id="panel1" class="tab-pane fade in active">
        <?= GridView::widget([
            'dataProvider' => $activeContractsProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedActiveColumns,
        ]); ?>
    </div>
    <div id="panel3" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $pendingContractsProvider,
            'filterModel'  => null,
            'pjax'         => true,
            'summary'      => false,
            'columns'      => $preparedPendingColumns,
        ]); ?>
    </div>
</div>
