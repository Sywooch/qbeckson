<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContractsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contracts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Contracts', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'number',
            'date',
            'status',
            //'status_termination',
            //'status_comment:ntext',
            //'status_year',
             'link_doc',
             'link_ofer',
            // 'start_edu_programm',
            // 'start_edu_contract',
            // 'stop_edu_contract',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{permit}{view}{update}{delete}',
                 'buttons' =>
                     [
                         'permit' => function ($url, $model) {
                             return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['/contracts/ok', 'id' => $model->id]), [
                                 'title' => Yii::t('yii', 'Подтвердить создание договора')
                             ]); },
                     ]
             ],
        ],
    ]); ?>
</div>
