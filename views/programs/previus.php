<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use app\models\Certificates;
use app\models\YearsSearch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProgramsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Поиск программ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-index">

    <h1><?= Html::encode($this->title) ?></h1>
   
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
    'pjax'=>true,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'program_id',
            //'organization_id',
            //'verification',
            [
                'class'=>'kartik\grid\ExpandRowColumn',
                'width'=>'50px',
                'value'=>function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail'=>function ($model, $key, $index, $column) {
                    $searchModel = new YearsSearch();
                    $searchModel->program_id = $model->id;
                    $searchModel->open = 1;
                    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                    
                    return Yii::$app->controller->renderPartial('/years/previus', ['searchModel'=>$searchModel, 'dataProvider'=>$dataProvider]);
                },
                'headerOptions'=>['class'=>'kartik-sheet-style'], 
                'expandOneOnly'=>true
            ],
            'year',
            'name',
             'organization.name',
            //'annotation',
             //'price',
             //'normative_price',
             //'rating',
             //'limit',
            // 'study',
            // 'open',
            // 'goal:ntext',
            // 'task:ntext',
            // 'annotation:ntext',
            // 'hours',
            // 'ovz',
            // 'quality_control',
            // 'link',
            // 'certification_date',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{favorites}',
                 'buttons' =>
                     [
                         'permit' => function ($url, $model) {
                             return Html::a('Записаться на программу', Url::to(['/contracts/new', 'id' => $model->id]), [
                                 'class' => 'btn btn-success',
                                 'title' => Yii::t('yii', 'Записаться на программу')
                             ]); },

                         'favorites' => function ($url, $model) {
                                $certificates = new Certificates();
                                $certificate = $certificates->getCertificates();

                             $rows = (new \yii\db\Query())
                                ->from('favorites')
                                ->where(['certificate_id' => $certificate['id']])
                                ->andWhere(['program_id' => $model->id])
                                ->andWhere(['type' => 1])
                                ->one();
                             if (!$rows) {
                                  return Html::a('<span class="glyphicon glyphicon-star"></span>', Url::to(['/favorites/new', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Добавить в избранное')
                                 ]);
                             }
                        },
                     ]
             ],
        ],
    ]); ?>
</div>
