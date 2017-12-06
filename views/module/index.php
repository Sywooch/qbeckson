<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Programme Modules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programme-module-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Programme Module', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'program_id',
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
            // 'previus',
            // 'quality_control',
            // 'p21z',
            // 'p22z',
            // 'results:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?></div>
