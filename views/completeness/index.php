<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CompletenessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Completenesses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="completeness-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Completeness', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'group_id',
            'month',
            'year',
            'completeness',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
