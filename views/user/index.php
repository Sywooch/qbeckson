<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            'password',
            //'access_token',
            //'auth_key',

             ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{permit}&nbsp;&nbsp;{delete}',
                 'buttons' =>
                     [
                         'permit' => function ($url, $model) {
                             return Html::a('<span class="glyphicon glyphicon-wrench"></span>', Url::to(['/permit/user/view', 'id' => $model->id]), [
                                 'title' => Yii::t('yii', 'Change user role')
                             ]); },
                     ]
             ],
        ],
    ]); ?>
</div>
