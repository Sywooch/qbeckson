<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MunSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var $dataProviderApplication yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Муниципалитеты');
$this->params['breadcrumbs'][] = $this->title;
$items = [
    [
        'label' => 'Муниципалитеты',
        'active' => true,
        'content' => GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'summary' => false,
            'columns' => [
                [
                    'attribute' => 'name',
                    'label' => 'Наименование муниципального района (городского округа)',
                ],
                [
                    'attribute' => 'deystv',
                    'label' => 'Действующих сертификатов',
                ],
                [
                    'attribute' => 'countdet',
                    'label' => 'Детей, от 5-ти до 18-ти',
                ],
                [
                    'attribute' => 'lastdeystv',
                    'label' => 'Предыдущий год',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}'
                ],
            ],
        ])
    ],
    [
        'label' => 'Изменения параметров',
        'content' => GridView::widget([
            'dataProvider' => $dataProviderApplication,
            'filterModel' => $searchModel,
            'pjax' => true,
            'summary' => false,
            'columns' => [
                [
                    'attribute' => 'name',
                    'label' => 'Наименование муниципального района (городского округа)',
                ],
                [
                    'attribute' => 'deystv',
                    'label' => 'Действующих сертификатов',
                ],
                [
                    'attribute' => 'countdet',
                    'label' => 'Детей, от 5-ти до 18-ти',
                ],
                [
                    'attribute' => 'lastdeystv',
                    'label' => 'Предыдущий год',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}'
                ],
            ],
        ])
    ]
];
?>
<div class="mun-index">


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Добавить муниципалитет'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=  Tabs::widget(['items' => $items])?>
</div>
