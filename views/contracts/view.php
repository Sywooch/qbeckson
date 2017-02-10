<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\models\Programs;
use app\models\Certificates;
use app\models\Groups;
use app\models\Organization;

 //return var_dump($model);
/* @var $this yii\web\View */
/* @var $model app\models\Contracts */

$contractdate = explode('-', $model->date);
//if ($model->status == 1 || $model->status == 4 || $model->status == 2 ) { $this->title = 'Просмотр договора от '.$contractdate["2"].'.'.$contractdate[1].'.'.$contractdate[0].' № '.$model->number; }

if ($model->status == 1 || $model->status == 4 || $model->status == 2 ) { $this->title = 'Просмотр договора № '.$model->number; }

if ($model->status == 0 || $model->status == 3) { $this->title = 'Просмотр заявки'; }

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if (isset($roles['operators'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/operator-contracts']];
}
if (isset($roles['organizations'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/organization-contracts']];
}

if (isset($roles['payer'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/payer-contracts']];
}

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-view col-md-8 col-md-offset-2">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php $cert = Certificates::findOne($model->certificate_id);
    $program = Programs::findOne($model->program_id);
    $groups = Groups::findOne($model->group_id);
    $org = Organization::findOne($model->organization_id);


    if (isset($roles['certificate']) or isset($roles['operators']) or isset($roles['payer'])) {
        echo DetailView::widget([
            'model' => $org,
            'attributes' => [
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => Html::a($org->name, Url::to(['/organization/view', 'id' => $org->id]), ['class' => 'blue', 'target' => '_blank']),
                ],
                
            ],
        ]);
    }
        
    if (isset($roles['organizations']) or isset($roles['operators']) or isset($roles['payer'])) {
       echo DetailView::widget([
            'model' => $cert,
            'attributes' => [
                'fio_child',
                [
                    'attribute' => 'fio_parent',
                    'label' => 'Заказчик по договору',
                ],
                [
                    'attribute' => 'number',
                    'format' => 'raw',
                    'value' => Html::a($cert->number, Url::to(['/certificates/view', 'id' => $cert->id]), ['class' => 'blue', 'target' => '_blank']),
                ],
            ],
        ]);
    }        
    ?>
    
    <?php 
    if (!isset($roles['payer']) && !isset($roles['certificate'])) {
        echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'payers.name',
                'label' => 'Наименование плательщика',
                'format' => 'raw',
                'value' => Html::a($model->payers->name, Url::to(['/payers/view', 'id' => $model->payers->id]), ['class' => 'blue', 'target' => '_blank']),
            ]
        ],
    ]);
    }
    ?>
    
    <?= DetailView::widget([
        'model' => $program,
        'attributes' => [
            [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => Html::a($program->name, Url::to(['/programs/view', 'id' => $program->id]), ['class' => 'blue', 'target' => '_blank']),
            ],
            [
                    'label' => 'Год обучения',
                    'value' => $model->yearName($model->year),
            ],
        ],
    ]) ?>
    
    <?php 
    if (isset($roles['certificate'])) {
        echo DetailView::widget([
        'model' => $groups,
        'attributes' => [
            [
                    'attribute' => 'name',
                    //'format' => 'raw',
                    //'value' => Html::a($groups->name, Url::to(['/groups/contracts', 'id' => $groups->id]), ['class' => 'blue', //'target' => '_blank']),
            ],
            'address',
            'schedule',
        ],
    ]);
    }
    else {
         echo DetailView::widget([
        'model' => $groups,
        'attributes' => [
            [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => Html::a($groups->name, Url::to(['/groups/contracts', 'id' => $groups->id]), ['class' => 'blue', 'target' => '_blank']),
            ],
        ],
    ]);
    }?>
    
    <?php 
    if ($model->wait_termnate == 1 || $model->status == 2  || $model->status == 4) {
         if ($model->status != 4) {
             echo '<h3>Ожидается расторжение договора с первого числа следующего месяца</h3>';
         }

        echo DetailView::widget([
        'model' => $model,
        'attributes' => [
             [
                'attribute' =>  'terminator_user',
                'value' => $model->terminator_user == 1 ? 'Заказчик' : 'Образовательная организация',
            ],
            'status_comment',
        ]]);
    } 
    

   if ($model->status == 1) {
        $contracts = [
            [
            'label' => 'Посмотреть текст договора',
            'format'=>'raw',
            'value'=> Html::a('<span class="glyphicon glyphicon-download-alt"></span>', Url::to(['/contracts/mpdf', 'id' => $model->id])),
            ],
            [
                'attribute' => 'status',
                'value'=> $model->statusName($model->status),
            ],
            'start_edu_contract:date',
            'stop_edu_contract:date',
            'all_funds',
            'funds_cert',
            'all_parents_funds',
            'rezerv',
            'paid',
        ];
       
    }
  
    
    if ($model->status == 3) {
        $contracts = [
            [
            'label' => 'Посмотреть текст договора',
            'format'=>'raw',
            'value'=> Html::a('<span class="glyphicon glyphicon-download-alt"></span>', Url::to(['/contracts/mpdf', 'id' => $model->id])),
            ],
            [
                'attribute' => 'status',
                'value'=> $model->statusName($model->status),
            ],
            'start_edu_contract:date',
            'stop_edu_contract:date',
            'all_funds',
            'funds_cert',
            'all_parents_funds',
            'rezerv',
        ];
       
    }
  
    
    
    if ($model->status == 0) {
        $contracts = [
            
            [
                'attribute' => 'status',
                'value'=> $model->statusName($model->status),
            ],
            'start_edu_contract:date',
            'stop_edu_contract:date',
            'all_funds',
            'funds_cert',
            'all_parents_funds',
            'rezerv',
        ];
       
    }
    
    if ($model->status == 4) {
       
        $contracts = [
            [
            'label' => 'Посмотреть текст договора',
            'format'=>'raw',
            'value'=> Html::a('<span class="glyphicon glyphicon-download-alt"></span>', Url::to(['/contracts/mpdf', 'id' => $model->id])),
            ],
            [
                'attribute' => 'status',
                'value'=> $model->statusName($model->status),
            ],
            'start_edu_contract:date',
            'stop_edu_contract:date',
            'date_termnate:date',
        ];
    }
        
         if ($model->status == 2) {
       
        $contracts = [
            [
            'label' => 'Посмотреть текст договора',
            'format'=>'raw',
            'value'=> Html::a('<span class="glyphicon glyphicon-download-alt"></span>', Url::to(['/contracts/mpdf', 'id' => $model->id])),
            ],
            [
                'attribute' => 'status',
                'value'=> $model->statusName($model->status),
            ],
            'start_edu_contract:date',
            'stop_edu_contract:date',
        ];
       
    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $contracts,
    ]) ?>
    
    
    <?php
    if ($model->ocenka == 1 && !$roles['payer']) {
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        if (!$roles['organizations']) {
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ocen_fact',
            'ocen_kadr',
            'ocen_mat',
            'ocen_obch',
        ],
    ]);
    }        
    }
    ?>
    
    
    
   <?php
       if (isset($roles['certificate'])) {
           $certificates = new Certificates();
                                    $certificate = $certificates->getCertificates();
           
           echo Html::a('Назад', Url::to(['/personal/certificate-contracts', 'id' => $model->id]), ['class' => 'btn btn-primary']);
           echo '&nbsp;';
           
           if ($certificate->actual == 1 && $model->status == 1) {
               echo Html::a(!$model->ocenka ? 'Оценить программу' : 'Изменить оценку', Url::to(['/contracts/ocenka', 'id' => $model->id]), ['class' => 'btn btn-success']);
               if ($model->status == 1 or $model->status == 4) {
                   echo '&nbsp;';
                   echo Html::a('Оставить возражение', Url::to(['/disputes/create', 'id' => $model->id]), ['class' => 'btn btn-warning']);
                }
           }
       }
        if (isset($roles['organizations'])) {
           echo Html::a('Назад', Url::to(['/personal/organization-contracts', 'id' => $model->id]), ['class' => 'btn btn-primary']);
            if ($model->status == 1 or $model->status == 4) {
                   echo '&nbsp;';
                   echo Html::a('Просмотреть возражения', Url::to(['/disputes/create', 'id' => $model->id]), ['class' => 'btn btn-warning']);
                }
       }
        if (isset($roles['operators'])) {
           echo Html::a('Назад', Url::to(['/personal/operator-contracts', 'id' => $model->id]), ['class' => 'btn btn-primary']);
            if ($model->status == 1 or $model->status == 4) {
                   echo '&nbsp;';
                   echo Html::a('Просмотреть возражения', Url::to(['/disputes/create', 'id' => $model->id]), ['class' => 'btn btn-warning']);
                }
       }
        if (isset($roles['payer'])) {
           echo Html::a('Назад', Url::to(['/personal/payer-contracts', 'id' => $model->id]), ['class' => 'btn btn-primary']);
            if ($model->status == 1 or $model->status == 4) {
                   echo '&nbsp;';
                   echo Html::a('Просмотреть возражения', Url::to(['/disputes/create', 'id' => $model->id]), ['class' => 'btn btn-warning']);
            }
       }
    ?>
   <?php
    if ($model->wait_termnate != 1 && isset($roles['certificate']) && $model->status == 1) {
        if ($certificate->actual == 1) {
            if (date("m") == 12) {
                $month = 1;
                $year = date("Y")+1;
            }
            else {
                $month = date("m")+1;
                $year = date("Y");
            }
             echo '<div class="pull-right">';
            echo Html::a('Расторгнуть договор', Url::to(['/contracts/terminate', 'id' => $model->id]), ['class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы собираетесь расторгнуть выбранный договор. Расторжение договора осуществляется с первого дня месяца, следующего за месяцем направления уведомления о расторжении договора. 
                    В случае если Вы подтвердите расторжение договора будет запущена процедура расторжения договора, которая не имеет обратной силы. Средства сертификата, зарезервированные на оплату договора и не использованные на данный момент, вернутся на баланс Вашего сертификата первого числа следующего месяца.
                    Вы действительно хотите расторгнуть данный договор с 1.'.$month.'.'.$year.'?']]);
            echo '</div>';
        }
    }
    
    if (isset($roles['certificate']) && $model->status == 0) {
        if ($certificate->actual == 1) {
            if (date("m") == 12) {
                $month = 1;
                $year = date("Y")+1;
            }
            else {
                $month = date("m")+1;
                $year = date("Y");
            }
             echo '<div class="pull-right">';
            echo Html::a('Отменить заявку', Url::to(['/contracts/termrequest', 'id' => $model->id]), ['class' => 'btn btn-danger']);
            echo '</div>';
        }
    }
    if (isset($roles['certificate']) && $model->status == 3) {
        if ($certificate->actual == 1) {
            if (date("m") == 12) {
                $month = 1;
                $year = date("Y")+1;
            }
            else {
                $month = date("m")+1;
                $year = date("Y");
            }
             echo '<div class="pull-right">';
            echo Html::a('Отменить заявку', Url::to(['/contracts/termrequest', 'id' => $model->id]), ['class' => 'btn btn-danger']);
            echo '</div>';
        }
    }
    ?> <!-- + 1 месяц !!! -->
    
    <?php
    if ($model->wait_termnate != 1 && isset($roles['organizations']) && $model->status == 1) {
        
        if (date("m") == 12) {
            $month = 1;
            $year = date("Y")+1;
        }
        else {
            $month = date("m")+1;
            $year = date("Y");
        }
        echo '<div class="pull-right">';
        echo Html::a('Расторгнуть договор', Url::to(['/contracts/terminate', 'id' => $model->id]), ['class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы собираетесь расторгнуть выбранный договор. Расторжение договора осуществляется с первого дня месяца, следующего за месяцем направления уведомления о расторжении договора. 
В случае если Вы подтвердите расторжение договора будет запущена процедура расторжения договора, которая не имеет обратной силы. Средства сертификата, зарезервированные на оплату договора и не использованные на данный момент, вернутся на баланс сертификата первого числа следующего месяца.
Вы действительно хотите расторгнуть данный договор с 1.'.$month.'.'.$year.'?']]);
         echo '</div>';
    }
    ?>

</div>
