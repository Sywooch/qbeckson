<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InformsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Informs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="informs-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Informs', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'program_id',
            'contract_id',
            'from',
            'text:ntext',
            // 'date',
            // 'read',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
