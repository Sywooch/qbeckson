<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use app\models\Certificates;
use app\models\Contracts;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProgrammeModuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="years-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
    'pjax'=>true,
        'summary' => false,
        //'showHeader' => false,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'program_id',
            //'verification',
            'year',
            //'month',
            // 'hours',
            // 'kvfirst',
            // 'kvdop',
            // 'hoursindivid',
            // 'hoursdop',
            // 'minchild',
            // 'maxchild',
             'price',
             'normative_price',
            // 'rating',
            // 'limits',
            // 'open',
             'hours',

           ['class' => 'yii\grid\ActionColumn',
                'template' => '{permit}',
                 'buttons' =>
                     [
                         'permit' => function ($url, $model) {
                             
                                    $contracts = new Contracts();
                                    $contract = $contracts->getContractsYear($model->id);
                                    if (empty($contract)) { $contract = 0; }
                                     if ($contract == 0) {
                                         return Html::a('Подробнее', Url::to(['/contracts/group', 'id' => $model->id]), [
                                             'class' => 'btn btn-success',
                                             'title' => Yii::t('yii', 'Записаться')
                                         ]); 
                                     }
                                },
                         'pred' => function ($url, $model) {
                             if ($model->previus == 1) {
                                 $certificates = new Certificates();
                                 $certificate = $certificates->getCertificates();

                                 $rows = (new \yii\db\Query())
                                    ->from('previus')
                                    ->where(['certificate_id' => $certificate['id']])
                                    ->andWhere(['year_id' => $model->id])
                                    ->one();
                                 if (!$rows) {
                                     return Html::a('Предварительная запись', Url::to(['/favorites/prev', 'id' => $model->id]), [
                                         'class' => 'btn btn-success',
                                         'title' => Yii::t('yii', 'Предварительная запись')
                                     ]); 
                                 }
                             }
                        },
                     ]
             ],
        ],
    ]); ?>
</div>
