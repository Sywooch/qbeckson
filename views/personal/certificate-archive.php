<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
    $this->title = 'Действующие договоры';
    $this->params['breadcrumbs'][] = $this->title;
?>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Расторгнутые договоры</a></li>
    <li><a data-toggle="tab" href="#panel2">Отклоненные заявки</a></li>
</ul>
<br>

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <?= GridView::widget([
            'dataProvider' => $Contracts4Provider,
            'filterModel' => $Contracts4Search,
            'summary' => false,
            'rowOptions' => function ($model, $index, $widget, $grid){
                  if($model->wait_termnate == 1){
                    return ['class' => 'danger'];
                  }
            },
            'columns' => [
             //['class' => 'yii\grid\SerialColumn'],
[
                    'attribute' => 'number',
                    'label' => 'Номер',
                ],
                [
                    'attribute' => 'date',
                    'format' => 'date',
                    'label' => 'Дата',
                ],            
                [
                    'attribute' => 'program',
                    'label' => 'Программа',
                    'format' => 'raw',
                    'value'=> function($data){
                        
                        $program = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('programs')
                            ->where(['name' => $data->program->name])
                            ->one();
                        
                        
                    return Html::a($data->program->name, Url::to(['/programs/view', 'id' => $program['id']]), ['class' => 'blue', 'target' => '_blank']);
                    },
                ], 
                
                //'status_termination',
                //'status_comment:ntext',
                //'status_year',
                [
                    'attribute' => 'organization',
                    'label' => 'Организация',
                    'format' => 'raw',
                    'value'=> function($data){
                        
                        $organization = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('organization')
                            ->where(['name' => $data->organization->name])
                            ->one();
                        
                        
                    return Html::a($data->organization->name, Url::to(['/organization/view', 'id' => $organization['id']]), ['class' => 'blue', 'target' => '_blank']);
                    },
                ],         
                [
                    'attribute' => 'paid',
                    'label' => 'Списано',
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
                'date_termnate:date',
                ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                 'controller' => 'contracts',
                ],
            ],
        ]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
         <?= GridView::widget([
            'dataProvider' => $Contracts2Provider,
            'filterModel' => $Contracts2Search,
            'summary' => false,
            'pjax'=>true,
            'columns' => [
                [
                    'attribute' => 'program',
                    'label' => 'Программа',
                    'format' => 'raw',
                    'value'=> function($data){
                        
                        $program = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('programs')
                            ->where(['name' => $data->program->name])
                            ->one();
                        
                        
                    return Html::a($data->program->name, Url::to(['/programs/view', 'id' => $program['id']]), ['class' => 'blue', 'target' => '_blank']);
                    },
                ], 
                [
                    'attribute' => 'organization',
                    'label' => 'Организация',
                    'format' => 'raw',
                    'value'=> function($data){
                        
                        $organization = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('organization')
                            ->where(['name' => $data->organization->name])
                            ->one();
                        
                        
                    return Html::a($data->organization->name, Url::to(['/organization/view', 'id' => $organization['id']]), ['class' => 'blue', 'target' => '_blank']);
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
</div>

