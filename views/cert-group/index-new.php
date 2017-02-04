<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CertGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Стоимость групп';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cert-group-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php // Html::a('Создать группу', ['create'], ['class' => 'btn btn-success']) ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
