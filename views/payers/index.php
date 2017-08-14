<?php

use app\helpers\GridviewHelper;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Mun;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PayersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Выбор плательщиков';
if (Yii::$app->user->can('organizations')) {
    $this->params['breadcrumbs'][] = ['label' => 'Плательщики', 'url' => ['/personal/organization-payers']];
}
$this->params['breadcrumbs'][] = $this->title;

$name = [
    'attribute' => 'name',
];
$phone = [
    'attribute' => 'phone',
];
$email = [
    'attribute' => 'email',
];
$fio = [
    'attribute' => 'fio',
];
$directionality = [
    'attribute' => 'directionality',
];
$mun = [
    'attribute' => 'mun',
    'value' => 'municipality.name',
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
];
$cooperates = [
    'attribute' => 'cooperates',
    'value' => function ($model) {
        /** @var \app\models\Payers $model */
        return $model->getCooperates()->andWhere(['status' => 1])->count();
    },
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 100
    ]
];
$certificates = [
    'attribute' => 'certificates',
    'value' => function ($model) {
        /** @var \app\models\Payers $model */
        return $model->getCertificates()->count();
    },
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
];
$actions = [
    'class' => ActionColumn::class,
    'controller' => 'payers',
    'template' => '{view}',
    'searchFilter' => false,
];

$columns = [
    $name,
    $phone,
    $email,
    $fio,
    $directionality,
    $mun,
    $cooperates,
    $certificates,
    $actions
];

$preparedColumns = GridviewHelper::prepareColumns('payers', $columns, 'open');

?>
<div class="payers-index">
    <?= SearchFilter::widget([
        'model' => $searchModel,
        'action' => ['personal/operator-payers'],
        'data' => GridviewHelper::prepareColumns(
            'payers',
            $columns,
            'all',
            'searchFilter',
            null
        ),
        'role' => UserIdentity::ROLE_ORGANIZATION,
        'type' => 'all'
    ]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'columns' => $preparedColumns,
    ]); ?>
    <?php
    if (Yii::$app->user->can('organizations')) {
        echo Html::a('Назад', '/personal/organization-payers', ['class' => 'btn btn-primary']);
    }
    ?>
</div>
