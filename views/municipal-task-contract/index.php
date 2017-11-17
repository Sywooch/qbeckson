<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\MunicipalTaskContractSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Municipal Task Contracts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="municipal-task-contract-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Municipal Task Contract', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'certificate_id',
            'payer_id',
            'program_id',
            'group_id',
            // 'status',
            // 'created_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
