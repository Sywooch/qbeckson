<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OperatorsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Operators');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operators-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Operators'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'name',
            'OGRN',
            'INN',
            // 'KPP',
            // 'OKPO',
            // 'address_legal',
            // 'address_actual',
            // 'phone',
            // 'email:email',
            // 'position',
            // 'fio',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
