<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CertificatesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Certificates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificates-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Certificates', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'number',
            'payer_id',
            'actual',
            // 'fio_child',
            // 'fio_parent',
            // 'nominal',
            // 'balance',
            // 'contracts',
            // 'directivity1',
            // 'directivity2',
            // 'directivity3',
            // 'directivity4',
            // 'directivity5',
            // 'directivity6',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
