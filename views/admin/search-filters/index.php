<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SettingsSearchFiltersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Настройки фильтров';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-search-filters-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'table_name',
            'table_columns:ntext',
            'inaccessible_columns:ntext',
            'is_active:boolean',
            'role',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
