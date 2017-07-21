<?php

use app\helpers\GridviewHelper;
use app\models\Mun;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

$this->title = 'Плательщики';
$this->params['breadcrumbs'][] = 'Плательщики';
/* @var $this yii\web\View */
/* @var $searchPayers \app\models\PayersSearch */
/* @var $payersProvider \yii\data\ActiveDataProvider */
?>
<?php
$columns = [
    'name',
    'phone',
    'email',
    'fio',
    'directionality',
    [
        'attribute' => 'mun',
        'value' => 'municipality.name',
        'type' => SearchFilter::TYPE_DROPDOWN,
        'data' => ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
    ],
    [
        'attribute' => 'cooperates',
        'value' => function ($model) {
            /** @var \app\models\Payers $model */
            return $model->getCooperates()->andWhere(['status' => 1])->count();
        },
        'type' => SearchFilter::TYPE_RANGE_SLIDER,
    ],
    [
        'attribute' => 'certificates',
        'value' => function ($model) {
            /** @var \app\models\Payers $model */
            return $model->getCertificates()->count();
        },
        'type' => SearchFilter::TYPE_RANGE_SLIDER,
    ],
    [
        'class' => ActionColumn::class,
        'controller' => 'payers',
        'template' => '{view}',
        'searchFilter' => false,
    ],
];
?>
<?= SearchFilter::widget([
    'model' => $searchPayers,
    'action' => ['personal/operator-payers'],
    'data' => GridviewHelper::prepareColumns('payers', $columns, 'searchFilter', null),
    'role' => UserIdentity::ROLE_OPERATOR,
    'type' => null
]); ?>
<p><?= Html::a('Добавить плательщика', ['payers/create'], ['class' => 'btn btn-success']) ?></p>
<?php $preparedColumns = GridviewHelper::prepareColumns('payers', $columns); ?>
<?= GridView::widget([
    'dataProvider' => $payersProvider,
    'filterModel' => null,
    'pjax' => true,
    'summary' => false,
    'columns' => $preparedColumns,
]); ?>
<?php array_pop($preparedColumns); ?>
<?= ExportMenu::widget([
    'dataProvider' => $payersProvider,
    'target' => '_self',
    'exportConfig' => [
        ExportMenu::FORMAT_EXCEL => false,
    ],
    'columns' => $preparedColumns,
]); ?>
