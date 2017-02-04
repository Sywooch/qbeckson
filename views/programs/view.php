<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use app\models\Organization;
use app\models\GroupsSearch;
use app\models\Payers;
use app\models\Certificates;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = $model->name;

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if (isset($roles['operators'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/operator-programs']];
}
if (isset($roles['organizations'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/organization-programs']];
}

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-view col-md-8 col-md-offset-2" ng-app>

  <?php
    if ($model->verification == 2) {
    if ($model->rating) {
        echo '<h1 class="pull-right">'.$model->rating.'%</h1>';
    }
    else {
        echo '<h4 class="pull-right">Рейтинга нет</h4>';
    }
    }
    ?>
   
    <h3><?= Html::encode($this->title) ?></h3>


    <?php
   
    if (isset($roles['organizations'])) {
         if ($model->verification == 2 || $model->verification == 0) {
             
        echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'directivity',
            [
                'attribute'=>'vid',
                'label' => 'Вид деятельности',
            ],
            'limit',
            [
                'label' => 'Возраст детей',
                'value'=> 'с '.$model->age_group_min.' лет до '.$model->age_group_max.' лет',
            ],
            [
                'attribute'=>'zab',
                'label' => 'Категория детей',
                'value'=> $model->zabName($model->zab, $model->ovz),
            ],
            'task:ntext',
            'annotation:ntext',
            
            [
                'attribute'=>'link',
                'format'=>'raw',
                'value'=>Html::a('<span class="glyphicon glyphicon-download-alt"></span>', '/'.$model->link),
            ],
            [
                'attribute'=>'mun',
                'value'=> $model->munName($model->mun),
            ],
            [
                'attribute'=>'ground',
                'value'=> $model->groundName($model->ground),
            ],
            [
                'attribute'=>'norm_providing',
                'label' => 'Нормы оснащения',
            ],


        ],
    ]); 
         } 
        if ($model->verification == 1) {
             echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'directivity',
            [
                'attribute'=>'vid',
                'label' => 'Вид деятельности',
            ],
            [
                'label' => 'Возраст детей',
                'value'=> 'с '.$model->age_group_min.' лет до '.$model->age_group_max.' лет',
            ],
            [
                'attribute'=>'zab',
                'label' => 'Категория детей',
                'value'=> $model->zabName($model->zab, $model->ovz),
            ],
            'task:ntext',
            'annotation:ntext',
            
            [
                'attribute'=>'link',
                'format'=>'raw',
                'value'=>Html::a('<span class="glyphicon glyphicon-download-alt"></span>', '/'.$model->link),
            ],
            [
                'attribute'=>'mun',
                'value'=> $model->munName($model->mun),
            ],
            [
                'attribute'=>'ground',
                'value'=> $model->groundName($model->ground),
            ],
            [
                'attribute'=>'norm_providing',
                'label' => 'Нормы оснащения',
            ],


        ],
    ]); 
         }
        if ($model->verification == 3) {
             echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Причина отказа',
                'value'=> $model->otkazName($model->id),
            ],
            'directivity',
            [
                'attribute'=>'vid',
                'label' => 'Вид деятельности',
            ],
            [
                'label' => 'Возраст детей',
                'value'=> 'с '.$model->age_group_min.' лет до '.$model->age_group_max.' лет',
            ],
            [
                'attribute'=>'zab',
                'label' => 'Категория детей',
                'value'=> $model->zabName($model->zab, $model->ovz),
            ],
            'task:ntext',
            'annotation:ntext',
            
            [
                'attribute'=>'link',
                'format'=>'raw',
                'value'=>Html::a('<span class="glyphicon glyphicon-download-alt"></span>', '/'.$model->link),
            ],
            [
                'attribute'=>'mun',
                'value'=> $model->munName($model->mun),
            ],
            [
                'attribute'=>'ground',
                'value'=> $model->groundName($model->ground),
            ],
            [
                'attribute'=>'norm_providing',
                'label' => 'Нормы оснащения',
            ],


        ],
    ]); 
         }
    }
    else {
        if (isset($roles['operators'])) {
            if ($model->verification == 3) {
                echo DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'label' => 'Причина отказа',
                            'value'=> $model->otkazName($model->id),
                        ],
                        [
                            'attribute'=>'organization.name',
                            'format' => 'raw',
                            'value'=> Html::a($model->organization->name, Url::to(['/organization/view', 'id' => $model->organization->id]), ['class' => 'blue']),
                        ],
                        'directivity',
                        [
                            'attribute'=>'vid',
                            'label' => 'Вид деятельности',
                        ],
                        'limit',
                        [
                            'label' => 'Возраст детей',
                            'value'=> 'с '.$model->age_group_min.' лет до '.$model->age_group_max.' лет',
                        ],
                        [
                            'attribute'=>'zab',
                            'label' => 'Категория детей',
                            'value'=> $model->zabName($model->zab, $model->ovz),
                        ],
                        'task:ntext',
                        'annotation:ntext',

                        [
                            'attribute'=>'link',
                            'format'=>'raw',
                            'value'=>Html::a('<span class="glyphicon glyphicon-download-alt"></span>', '/'.$model->link),
                        ],
                        [
                            'attribute'=>'mun',
                            'value'=> $model->munName($model->mun),
                        ],
                        [
                            'attribute'=>'ground',
                            'value'=> $model->groundName($model->ground),
                        ],
                        [
                            'attribute'=>'norm_providing',
                            'label' => 'Нормы оснащения',
                        ],


                    ],
                ]);
            }
            else {
                echo DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'attribute'=>'organization.name',
                            'format' => 'raw',
                            'value'=> Html::a($model->organization->name, Url::to(['/organization/view', 'id' => $model->organization->id]), ['class' => 'blue']),
                        ],
                        'directivity',
                        [
                            'attribute'=>'vid',
                            'label' => 'Вид деятельности',
                        ],
                        'limit',
                        [
                            'label' => 'Возраст детей',
                            'value'=> 'с '.$model->age_group_min.' лет до '.$model->age_group_max.' лет',
                        ],
                        [
                            'attribute'=>'zab',
                            'label' => 'Категория детей',
                            'value'=> $model->zabName($model->zab, $model->ovz),
                        ],
                        'task:ntext',
                        'annotation:ntext',

                        [
                            'attribute'=>'link',
                            'format'=>'raw',
                            'value'=>Html::a('<span class="glyphicon glyphicon-download-alt"></span>', '/'.$model->link),
                        ],
                        [
                            'attribute'=>'mun',
                            'value'=> $model->munName($model->mun),
                        ],
                        [
                            'attribute'=>'ground',
                            'value'=> $model->groundName($model->ground),
                        ],
                        [
                            'attribute'=>'norm_providing',
                            'label' => 'Нормы оснащения',
                        ],


                    ],
                ]);
            }
        }
        else {
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute'=>'organization.name',
                        'format' => 'raw',
                        'value'=> Html::a($model->organization->name, Url::to(['/organization/view', 'id' => $model->organization->id]), ['class' => 'blue']),
                    ],
                    'directivity',
                    [
                        'attribute'=>'vid',
                        'label' => 'Вид деятельности',
                    ],
                    'limit',
                    [
                        'label' => 'Возраст детей',
                        'value'=> 'с '.$model->age_group_min.' лет до '.$model->age_group_max.' лет',
                    ],
                    [
                        'attribute'=>'zab',
                        'label' => 'Категория детей',
                        'value'=> $model->zabName($model->zab, $model->ovz),
                    ],
                    'task:ntext',
                    'annotation:ntext',

                    [
                        'attribute'=>'link',
                        'format'=>'raw',
                        'value'=>Html::a('<span class="glyphicon glyphicon-download-alt"></span>', '/'.$model->link),
                    ],
                    [
                        'attribute'=>'mun',
                        'value'=> $model->munName($model->mun),
                    ],
                    [
                        'attribute'=>'ground',
                        'value'=> $model->groundName($model->ground),
                    ],
                    [
                        'attribute'=>'norm_providing',
                        'label' => 'Нормы оснащения',
                    ],


                ],
            ]); 
        }
    }
    ?>
    
    <?php
    $payers = new Payers();
    $payer = $payers->getPayer();
        
    if (isset($roles['payer'])) {
        $link = '/personal/payer-contracts';
    }
        
    if (isset($roles['operators'])) {
        $link = '/personal/operator-contracts';
    }
        
     if (isset($roles['organizations'])) {
        $link = '/personal/organization-contracts';
    }
        
     if ($model->verification == 2 && !isset($roles['certificate'])) {
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => Html::a('Число обучающихся по программе', Url::to([$link, 'prog' => $model->name]), ['class' => 'blue', 'target' => '_blank']),
                'value'=> $model->countContract($model->id, $payer),
            ],
        ],
    ]);
     }?>
    
        <?php 
    if (isset($roles['payer'])) {
        
        DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ocen_fact',
            'ocen_kadr',
            'ocen_mat',
            'ocen_obch',
            'quality_control',
        ],
    ]);
    }
    
    ?>
    
    <?php 
     //return var_dump($roles);
    if (isset($roles['operators'])) {
       /*
        $ocen_fact_count = (new \yii\db\Query())
            ->select(['ocen_fact'])
            ->from('contracts')
            ->where(['program_id' => $model->id])
            ->count();
        
        $ocen_fact_column = (new \yii\db\Query())
            ->select(['ocen_fact'])
            ->from('contracts')
            ->where(['program_id' => $model->id])
            ->column();
        
        $ocen_fact_sum = 0;
        foreach ($ocen_fact_column as $column)
        {
            $ocen_fact_sum = $ocen_fact_sum + $column;
        }
        
        
        $ocen_kadr_count = (new \yii\db\Query())
            ->select(['ocen_kadr'])
            ->from('contracts')
            ->where(['program_id' => $model->id])
            ->count();
        
        $ocen_kadr_column = (new \yii\db\Query())
            ->select(['ocen_kadr'])
            ->from('contracts')
            ->where(['program_id' => $model->id])
            ->column();
        
        $ocen_kadr_sum = 0;
        foreach ($ocen_kadr_column as $column)
        {
            $ocen_kadr_sum = $ocen_kadr_sum + $column;
        }
        
        $ocen_mat_count = (new \yii\db\Query())
            ->select(['ocen_mat'])
            ->from('contracts')
            ->where(['program_id' => $model->id])
            ->count();
        
        $ocen_mat_column = (new \yii\db\Query())
            ->select(['ocen_mat'])
            ->from('contracts')
            ->where(['program_id' => $model->id])
            ->column();
        
        $ocen_mat_sum = 0;
        foreach ($ocen_mat_column as $column)
        {
            $ocen_mat_sum = $ocen_mat_sum + $column;
        }
        
        $ocen_obch_count = (new \yii\db\Query())
            ->select(['ocen_obch'])
            ->from('contracts')
            ->where(['program_id' => $model->id])
            ->count();
        
        $ocen_obch_column = (new \yii\db\Query())
            ->select(['ocen_obch'])
            ->from('contracts')
            ->where(['program_id' => $model->id])
            ->column();
        
        $ocen_obch_sum = 0;
        foreach ($ocen_obch_column as $column)
        {
            $ocen_obch_sum = $ocen_obch_sum + $column;
        }
        
        */
        
       echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            
            [
                'attribute'=>'ocen_fact',
             //   'value'=> $ocen_fact_sum / $ocen_fact_count,
            ],
            [
                'attribute'=>'ocen_kadr',
               // 'value'=> $ocen_kadr_sum / $ocen_kadr_count,
            ],
            [
                'attribute'=>'ocen_mat',
                //'value'=> $ocen_mat_sum / $ocen_mat_count,
            ],
            [
                'attribute'=>'ocen_obch',
             //   'value'=> $ocen_obch_sum / $ocen_obch_count,
            ],
        ],
    ]);
    }
    
    ?>
    
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'year',
                'value'=> $model->yearName($model->year),
            ],
        ],
    ]) ?>
    
    
    
    <?php
    if (!empty($years)) {
        
        $countyears = count($years);
        $i = 1;
        $form = ActiveForm::begin(); 
        foreach ($years as $value) {
            
            echo '<div class="well">';
            if ($value->year == 1) { $label = 'Первый год обучения'; }
            if ($value->year == 2) { $label = 'Второй год обучения'; }
            if ($value->year == 3) { $label = 'Третий год обучения'; }
            if ($value->year == 4) { $label = 'Четвертый год обучения'; }
            if ($value->year == 5) { $label = 'Пятый год обучения'; }
            if ($value->year == 6) { $label = 'Шестой год обучения'; }
            if ($value->year == 7) { $label = 'Седьмой год обучения'; }
            
            
            if (isset($roles['certificate'])) {  
                
                $certificates = new Certificates();
                $cert = $certificates->getCertificates();
                
                if ($value->previus == 1 && $cert->actual == 1) {
                                 $certificates = new Certificates();
                                 $certificate = $certificates->getCertificates();

                                 $rows = (new \yii\db\Query())
                                    ->from('previus')
                                    ->where(['certificate_id' => $certificate['id']])
                                    ->andWhere(['year_id' => $value->id])
                                    ->andWhere(['actual' => 1])
                                    ->one();
                                 /*if (!$rows) {
                                     echo Html::a('Предварительная запись', Url::to(['/favorites/prev', 'id' => $value->id]), [
                                         'class' => 'btn btn-success pull-right',
                                         'title' => Yii::t('yii', 'Предварительная запись')
                                     ]); 
                                     if ($countyears == 1) { echo '<br><br>';}
                                 } else {
                                     echo Html::a('Отменить предварительную запись', Url::to(['/favorites/disprev', 'id' => $value->id]), [
                                         'class' => 'btn btn-danger pull-right',
                                         'title' => Yii::t('yii', 'Предварительная запись')
                                     ]);  
                                     if ($countyears == 1) { echo '<br><br>';}
                                 } */
                             }
            }
            
            if ($countyears > 1) {
                echo $form->field($value, 'selectyear'.$i)->checkbox(['value' => 1, 'ng-model' => 'selectyear'.$i])->label($label);
            }
            
            $group = [
                        'price',
                        'normative_price',
                        'month',
                        [
                            'label'=> 'Часов по учебному плану',
                            'attribute'=> 'hours',
                        ],
                        [
                            'label'=> 'Наполняемость группы',
                            'value'=> 'от '.$value->minchild.' до '.$value->maxchild,
                        ],
                        [
                            'label'=> 'Квалификация руководителя кружка',
                            'attribute'=> 'kvfirst',
                        ],
                    ];
            
            if ($value->hoursindivid) {
                array_push($group, 
                           [
                            'label'=> 'Часов индивидуальных консультаций',
                            'attribute'=> 'hoursindivid',
                            ]
                );
            }
            
            if ($value->hoursdop) {
                array_push($group, 
                           [
                            'label'=> 'Часов работы дополнительного педагога',
                            'attribute'=> 'hoursdop',
                        ],
                           [
                            'label'=> 'Квалификация дополнительного педагога',
                            'attribute'=> 'kvdop',
                        ]
                );
            }
            
            if ($countyears > 1) {
             echo '<div ng-show="selectyear'.$i.'">';
            }
            
            echo DetailView::widget([
                'model' => $value,
                'attributes' => $group,
            ]);
            
            if ($model->verification == 2) {
            
                $searchGroups = new GroupsSearch();
                $searchGroups->year_id = $value->id;
                $GroupsProvider = $searchGroups->search(Yii::$app->request->queryParams);

                    
                if (isset($roles['certificate'])) {    
                
                    $count1 = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('contracts')
                        ->where(['status'=> [0,1,3]])
                        ->andWhere(['program_id' => $model->id])
                        ->count();

                    $count2 = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('contracts')
                        ->where(['status'=> [0,1,3,5]])
                        ->andWhere(['organization_id' => $model->organization_id])
                        ->count();

                    $organization = Organization::findOne($model->organization_id);

                    //echo var_dump($organization->max_child);

                    $certificates = new Certificates();
                    $certificate = $certificates->getCertificates();

                    $programscolumn = (new \yii\db\Query())
                                ->select(['id'])
                                ->from('programs')
                                ->where(['directivity' => $model->directivity])
                                ->column();


                    $count3 = (new \yii\db\Query())
                                ->select(['id'])
                                ->from('contracts')
                                ->where(['status'=> [0,1,3]])
                                ->andWhere(['payer_id' => $certificate->payer_id])
                                ->andWhere(['program_id' => $programscolumn])
                                ->count();

                    $payer = Payers::findOne($certificate->payer_id);

                    if ($model->directivity == 'Техническая (робототехника)') { 
                        $limit_napr = $payer->directionality_1rob_count;
                    }
                    if ($model->directivity == 'Техническая (иная)') { $limit_napr = $payer->directionality_1_count; }
                    if ($model->directivity == 'Естественнонаучная') { $limit_napr = $payer->directionality_2_count; }
                    if ($model->directivity == 'Физкультурно-спортивная') { $limit_napr = $payer->directionality_3_count; }
                    if ($model->directivity == 'Художественная') { $limit_napr = $payer->directionality_4_count; }
                    if ($model->directivity == 'Туристско-краеведческая') { $limit_napr = $payer->directionality_5_count; }
                    if ($model->directivity == 'Социально-педагогическая') { $limit_napr = $payer->directionality_6_count; }

                    //echo $count3;
                    //echo $limit_napr;
                    
                    if ($cooperate != 0) {  
                        if ($value->open == 0) { 
                            echo '<h4>Вы не можете записаться на программу. Зачисление закрыто.</h4>';
                        }
                        else {
                            if ($certificate->balance == 0) {
                                echo '<h4>Вы не можете записаться на программу. Нет свободных средств на сертификате.</h4>';
                            }
                            else {
                                if ($organization->actual == 0) {
                                    echo '<h4>Вы не можете записаться на программу. Действие организации приостановленно.</h4>';
                                }
                                else {
                                    if ($count3 >= $limit_napr) {
                                         echo '<h4>Вы не можете записаться на программу. Достигнут максимальный предел числа одновременно оплачиваемых вашей уполномоченной организацией услуг по данной направленности.</h4>';
                                    }
                                    else {
                                        if ($organization->max_child <= $count2) { 
                                            echo '<h4>Вы не можете записаться на программу. Достигнут максимальный лимит зачисления в организацию. Свяжитесь с представителем организации.</h4>';
                                        }
                                        else {
                                            if ($model->limit <= $count1) {
                                                echo '<h4>Достигнут максимальный лимит зачисления на обучение по программе. Свяжитесь с представителем организации.</h4>';
                                            }
                                            else {    
                                                

                                                    $certificates = new Certificates();
                                                    $certificate = $certificates->getCertificates();

                                                    $myprog = (new \yii\db\Query())
                                                        ->select(['program_id'])
                                                        ->from('contracts')
                                                        ->where(['certificate_id'=> $certificate['id']])
                                                        ->andWhere(['status'=> [0,1,3]])
                                                        ->column();

                                                    if (in_array($model['id'], $myprog)) {
                                                        echo '<p>Вы уже подали заявку на программу/заключили договор на обучение</p>';
                                                    }
                                                
                                                else {
                                                echo '<p>Вы можете записаться на программу. Выберете группу:</p>';
                    
                                                echo GridView::widget([
                                                    'dataProvider' => $GroupsProvider,
                                                    'summary' => false,
                                                    'columns' => [

                                                        'name',
                                                        'address',
                                                        'schedule',
                                                        [
                                                            'attribute' => 'datestart',
                                                            'format' => 'date',
                                                            'label' => 'Начало',
                                                        ],
                                                        [
                                                            'attribute' => 'datestop',
                                                            'format' => 'date',
                                                            'label' => 'Конец',
                                                        ],
                                                        [
                                                            'label' => 'Мест',
                                                            'value'=> function ($model) {

                                                                $contract = (new \yii\db\Query())
                                                                    ->select(['id'])
                                                                    ->from('contracts')
                                                                    ->where(['status' => [0,1,3]])
                                                                    ->andWhere(['group_id' => $model->id])
                                                                    ->count();

                                                                $years = (new \yii\db\Query())
                                                                    ->select(['maxchild'])
                                                                    ->from('years')
                                                                    ->where(['id' => $model->year_id])
                                                                    ->one();

                                                            return $years['maxchild'] - $contract;
                                                            }
                                                        ],

                                                        ['class' => 'yii\grid\ActionColumn',
                                                            'template' => '{permit}',
                                                             'buttons' =>
                                                                 [
                                                                     'permit' => function ($url, $model) {


                                                                             $contract = (new \yii\db\Query())
                                                                                ->select(['id'])
                                                                                ->from('contracts')
                                                                                ->where(['status' => [0,1,3]])
                                                                                ->andWhere(['group_id' => $model->id])
                                                                                ->count();

                                                                            $years = (new \yii\db\Query())
                                                                                ->select(['maxchild'])
                                                                                ->from('years')
                                                                                ->where(['id' => $model->year_id])
                                                                                ->one();

                                                                              $certificates = new Certificates();
                                                                                $cert = $certificates->getCertificates();
                                                                             $free = $years['maxchild'] - $contract;

                                                                            //return var_dump($free);

                                                                             if ($free != 0 && $cert->actual == 1) {
                                                                                return Html::a('Выбрать', Url::to(['/contracts/new', 'id' => $model->id]), [
                                                                                     'class' => 'btn btn-success',
                                                                                     'title' => Yii::t('yii', 'Выбрать')
                                                                                 ]); 
                                                                             }

                                                                     },

                                                                 ]
                                                         ],
                                                    ],
                                                ]);
                                                }
                    
                                            }
                                        }
                                    }
                                }
                            }
                        }    
                    }                
                }
                else {
                echo GridView::widget([
                            'dataProvider' => $GroupsProvider,
                            'summary' => false,
                            'columns' => [

                                'name',
                                'address',
                                'schedule',
                                [
                                    'attribute' => 'datestart',
                                    'format' => 'date',
                                    'label' => 'Начало',
                                ],
                                [
                                    'attribute' => 'datestop',
                                    'format' => 'date',
                                    'label' => 'Конец',
                                ],
                                [
                                    'label' => 'Мест',
                                    'value'=> function ($model) {

                                        $contract = (new \yii\db\Query())
                                            ->select(['id'])
                                            ->from('contracts')
                                            ->where(['status' => [0,1,3]])
                                            ->andWhere(['group_id' => $model->id])
                                            ->count();

                                        $years = (new \yii\db\Query())
                                            ->select(['maxchild'])
                                            ->from('years')
                                            ->where(['id' => $model->year_id])
                                            ->one();

                                    return $years['maxchild'] - $contract;
                                    }
                                ],

                                ['class' => 'yii\grid\ActionColumn',
                                    'template' => '{view}',
                                     'buttons' =>
                                         [
                                             'view' => function ($url, $model) {
                                                 $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
                                                 if (isset($roles['organizations'])) {
                                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/groups/contracts', 'id' => $model->id])); 
                                                 }
                                             },
                                         ]
                                 ],
                            ],
                        ]);
                    
                    if (isset($roles['organizations'])) {
                        echo Html::a('Добавить группу', Url::to(['/groups/newgroup', 'id' => $value->id]), ['class' => 'btn btn-primary']);
                    }
                    
                    
                }
                    
                }
            if ($countyears > 1) {
            echo '</div>';
            }
            
            $i++;
              echo '</div>';
        }
        ActiveForm::end();
    }

     if (isset($roles['operators'])) {
         
            echo Html::a('Пересчитать нормативную стоимость', Url::to(['/programs/newnormprice', 'id' => $model->id]), ['class' => 'btn btn-primary']);
            echo '&nbsp;';
            echo Html::a('Пересчитать лимит', Url::to(['/programs/newlimit', 'id' => $model->id]), ['class' => 'btn btn-primary']);
            echo '&nbsp;';
            echo Html::a('Пересчитать рейтинг', Url::to(['/programs/raiting', 'id' => $model->id]), ['class' => 'btn btn-primary']);
            echo '<br><br>';
        echo Html::a('Назад', '/personal/operator-programs', ['class' => 'btn btn-primary']);
    }
    if (isset($roles['certificate'])) {
        echo Html::a('Назад', '/programs/search', ['class' => 'btn btn-primary']);
        /*echo '&nbsp;';
        echo Html::a('Удалить', Url::to(['/contracts/delete', 'id' => $cont['id']]), ['class' => 'btn btn-danger' ,
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post']]); */
    }
    if (isset($roles['payer'])) {
        echo Html::a('Назад', '/personal/payer-programs', ['class' => 'btn btn-primary']);
    }
    if (isset($roles['organizations'])) {
        echo Html::a('Назад', '/personal/organization-programs', ['class' => 'btn btn-primary']);
       
        $organizations = new Organization();
        $organization = $organizations->getOrganization();
        
        if ($organization['actual'] != 0) {
            echo "&nbsp;";
            $contracts = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('contracts')
                            ->where(['program_id' => $model->id, 'status' => 1])
                            ->count();
            
            $open = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('years')
                            ->where(['program_id' => $model->id])
                            ->andWhere(['open' => 1])
                            ->count();
            
                       // return var_dump($open);

            
            if ($open == 0 and $contracts == 0) {
                echo Html::a('Редактировать', Url::to(['/programs/update', 'id' => $model->id]), ['class' => 'btn btn-primary']);
                echo "&nbsp;";
                echo "<div class='pull-right'>";
                echo Html::a('Удалить', Url::to(['/programs/delete', 'id' => $model->id]), ['class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить программу?',
                'method' => 'post']]);
                echo "</div>";
            }
        }
    }

    ?>
</div>
