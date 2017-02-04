<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CertGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Номиналы групп';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cert-group-index col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php // Html::a('Создать группу', ['create'], ['class' => 'btn btn-success']) ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'payer_id',
            'group',
            'nominal',

            ['class' => 'yii\grid\ActionColumn',
            'template' => '{update}'],
        ],
    ]); ?>
</div>
