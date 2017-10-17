<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrganizationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ЛК Организации';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Organization', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'user_id',
            //'actual',
            //'type',
            'name',
            // 'license_date',
            // 'license_number',
            // 'license_issued',
            // 'requisites',
            // 'representative',
            'address_legal',
            //'geocode',
            //'max_child',
            [
                'attribute' => 'amount_child',
                'value' => function ($model) {
                    /** @var \app\models\Organization $model */
                    return $model->getChildren()->where('status = 1')->count();
                }
            ],
            'inn',
            'okopo',
            'raiting',
            // 'ground',
            //'user.id',
            //'user.username',
            //'user.password',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
