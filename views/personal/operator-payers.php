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
/* @var $allPayersProvider \yii\data\ActiveDataProvider */
?>
<?php
$columns = [
    [
        'attribute' => 'name',
    ],
    [
        'attribute' => 'phone',
    ],
    [
        'attribute' => 'email',
        'format' => 'email',
    ],
    [
        'attribute' => 'fio',
    ],
    [
        'attribute' => 'directionality',
    ],
    [
        'attribute' => 'mun',
        'value' => function ($model) {
            /** @var \app\models\Payers $model */
            return Html::a(
                $model->municipality->name,
                ['mun/view', 'id' => $model->municipality->id],
                ['target' => '_blank', 'data-pjax' => '0']
            );
        },
        'format' => 'raw',
        'type' => SearchFilter::TYPE_DROPDOWN,
        'data' => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
    ],
    [
        'attribute' => 'cooperates',
        'value' => function ($model) {
            /** @var \app\models\Payers $model */
            $cooperatesCount = $model->getCooperates()->andWhere(['status' => 1])->count();

            return $cooperatesCount > 0 ? $cooperatesCount : '-';
        },
        'type' => SearchFilter::TYPE_RANGE_SLIDER,
        'pluginOptions' => [
            'max' => 100,
        ],
    ],
    [
        'attribute' => 'certificates',
        'value' => function ($model) {
            /** @var \app\models\Payers $model */
            $certificatesCount = $model->getCertificates()->count();

            return $certificatesCount > 0 ? $certificatesCount : '-';
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
$preparedColumns = GridviewHelper::prepareColumns('payers', $columns);
?>
<?= SearchFilter::widget([
    'model' => $searchPayers,
    'action' => ['personal/operator-payers'],
    'data' => GridviewHelper::prepareColumns(
        'payers',
        $columns,
        null,
        'searchFilter',
        null
    ),
    'role' => UserIdentity::ROLE_OPERATOR,
    'type' => null
]); ?>
<p><?= Html::a('Добавить плательщика', ['payers/create'], ['class' => 'btn btn-success']) ?></p>
<?= GridView::widget([
    'dataProvider' => $payersProvider,
    'filterModel' => null,
    'pjax' => true,
    'summary' => false,
    'columns' => $preparedColumns,
]); ?>
<?php
echo $this->render('/common/_export', [
    'dataProvider' => $payersProvider,
    'columns' => $columns,
    'group' => 'operator-payers',
    'table' => 'payers',
]);
?>
