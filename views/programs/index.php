<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use app\models\Certificates;
use app\models\ProgrammeModuleSearch;

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
                    $searchModel = new ProgrammeModuleSearch();
                    $searchModel->program_id = $model->id;
                    $searchModel->open = 1;
                    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                    
                    return Yii::$app->controller->renderPartial('/years/detail', ['searchModel'=>$searchModel, 'dataProvider'=>$dataProvider]);
                },
                'headerOptions'=>['class'=>'kartik-sheet-style'], 
                'expandOneOnly'=>true
            ],
            'name',
            'organization.name',
            'year',
             'directivity',
            //'age_group_min',
            //'age_group_max',
             //'price',
             ['attribute' => 'ovz',
              'label' => 'Наличие ОВЗ',
                  'value' => function($data){
                         if ($data->ovz == 1) {
                             return 'Без ОВЗ';
                         } else {
                             return 'С ОВЗ';
                         }
                  }
             ],
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
                                  return Html::a('<span class="glyphicon glyphicon-star-empty"></span>', Url::to(['/favorites/new', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Добавить в избранное')
                                 ]);
                             } else {
                                  return Html::a('<span class="glyphicon glyphicon-star"></span>', Url::to(['/favorites/terminate', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Убрать из избранного')
                                 ]);
                             }
                        },
                     ]
             ],
        ],
    ]); ?>
</div>
