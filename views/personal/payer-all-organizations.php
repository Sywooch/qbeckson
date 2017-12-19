<?php

use yii\grid\GridView;
use yii\helpers\Html;


$this->title = 'Выберите организацию';
   $this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */
?>

<h1><?= $this->title ?></h1>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'summary' => false,
    'columns' => [
        'name',
        [
            'attribute' => 'type',
            'value' => function ($model) {
                /** @var \app\models\Organization $model */
                return $model::types()[$model->type];
            },
        ],
        'address_actual',
        'fio_contact',
        'phone',
        ['class' => 'yii\grid\ActionColumn',
            'controller' => 'organization',
            'template' => '{view}',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/organization/view-subordered', 'id' => $model->id]);
                },
            ],
        ],
    ],
]); ?>

