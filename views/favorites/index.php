<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FavoritesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Favorites';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="favorites-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Favorites', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'certificate_id',
            'program_id',
            'organization_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
