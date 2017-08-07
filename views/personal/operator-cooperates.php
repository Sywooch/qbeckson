<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

/* @var $searchAppealed app\models\search\CooperateSearch */
/* @var $searchActive app\models\search\CooperateSearch */
/* @var $appealedProvider yii\data\ActiveDataProvider */
/* @var $activeProvider yii\data\ActiveDataProvider */

$this->title = 'Соглашения';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    [
        'attribute' => 'organizationName',
        'label' => 'Организация',
        'format' => 'raw',
        'value' => function ($model) {
            /** @var \app\models\Cooperate $model */
            return Html::a(
                $model->organization->name,
                Url::to(['organization/view', 'id' => $model->organization->id]),
                ['target' => '_blank', 'data-pjax' => '0']
            );
        },
    ],
    [
        'attribute' => 'payerName',
        'label' => 'Плательщик',
        'format' => 'raw',
        'value' => function ($model) {
            /** @var \app\models\Cooperate $model */
            return Html::a(
                $model->payer->name,
                Url::to(['payers/view', 'id' => $model->payer->id]),
                ['target' => '_blank', 'data-pjax' => '0']
            );
        },
    ],
    'reject_reason:ntext',
    'appeal_reason:ntext',
    'created_date',
    [
        'class' => 'yii\grid\ActionColumn',
        'controller' => 'cooperate',
        'template' => '{view}'
    ]
];

?>
<div class="cooperate">
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
            <?= GridView::widget([
                'dataProvider' => $activeProvider,
                'filterModel' => $searchActive,
                'columns' => $columns,
            ]); ?>
        </div>
        <div id="panel2" class="tab-pane fade">
            <?= GridView::widget([
                'dataProvider' => $appealedProvider,
                'filterModel' => $searchAppealed,
                'columns' => $columns,
            ]); ?>
        </div>
    </div>
</div>
