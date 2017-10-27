<?php

use app\models\Help;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\HelpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Руководство';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="help-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать статью', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'сортировка',
                'buttons' => [
                    'order-increase' => function ($url, $model) {
                        /** @var Help $model */
                        return !$model->isOrderMax() ? Html::a('', $url, ['class' => 'glyphicon glyphicon-arrow-down']) : '';
                    },
                    'order-reduce' => function ($url, $model) {
                        /** @var Help $model */
                        return !$model->isOrderMin() ? Html::a('', $url, ['class' => 'glyphicon glyphicon-arrow-up']) : '';
                    }
                ],
                'template' => '{order-reduce} {order-increase}',
            ],
            'name',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>
