<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Ожидающие подтверждения договоры';
   $this->params['breadcrumbs'][] = $this->title;

?>

<div class="tab-content">
        <?= GridView::widget([
            'dataProvider' => $Contracts3Provider,
            'filterModel' => $Contracts3Search,
            'summary' => false,
            'pjax'=>true,
            'columns' => [
                [
                    'attribute' => 'program',
                    'label'     => 'Программа',
                    'format'    => 'raw',
                    'value'     => function ($model)
                    {
                        /** @var $model app\models\Contracts */
                        return Html::a($model->program->name, Url::to(['/programs/view', 'id' => $model->program_id]), ['class' => 'blue', 'target' => '_blank']);
                    },
                ], 
                
                //'status_termination',
                //'status_comment:ntext',
                //'status_year',
                [
                    'attribute' => 'organization',
                    'label'     => 'Организация',
                    'format'    => 'raw',
                    'value'     => function ($model)
                    {
                        /** @var $model app\models\Contracts */
                        return Html::a($model->organization->name, Url::to(['/organization/view', 'id' => $model->organization_id]), ['class' => 'blue', 'target' => '_blank']);
                    },
                ],         
                [
                    'attribute' => 'start_edu_contract',
                    'format' => 'date',
                    'label' => 'Начало обучения',
                ],
                [
                    'attribute' => 'stop_edu_contract',
                    'format' => 'date',
                    'label' => 'Конец обучения',
                ],               
                [
                    'attribute' => 'status',
                    'value'=> function($data){
                        if ($data->status == 0) { return 'Заявка ожидает подтверждения'; } 
                        if ($data->status == 3) { return 'Договор ожидает подписания'; } 
                    },
                ],
                 [
                    'attribute' => 'rezerv',
                    'label' => 'Зарезервировано средств',
                ],
                
                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'contracts',
                    'template' => '{view}',
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