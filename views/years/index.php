<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\YearsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Years');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="years-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Years'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'program_id',
            //'verification',
            'year',
            'month',
            // 'hours',
            // 'kvfirst',
            // 'kvdop',
            // 'hoursindivid',
            // 'hoursdop',
            // 'minchild',
            // 'maxchild',
            // 'price',
            // 'normative_price',
            // 'rating',
            // 'limits',
            // 'open',
            // 'quality_control',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
