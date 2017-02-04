<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvoicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoices-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Invoices', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'month',
            'organization_id',
            'payers_id',
            'sum',
            // 'number',
            // 'date',
            // 'link',
            // 'prepayment',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
