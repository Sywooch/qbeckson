<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */

$this->title = 'Просмотр группы: '.$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Группы', 'url' => ['/personal/organization-groups']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-view col-md-10 col-md-offset-1">
    
    <?php
    
    $contract1 = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('contracts')
                        ->where(['status' => 1])
                        ->andWhere(['group_id' => $model->id])
                        ->count();
    
    $contract2 = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('contracts')
                        ->where(['status' => [0,3]])
                        ->andWhere(['group_id' => $model->id])
                        ->count();
    
    $contract3 = (new \yii\db\Query())
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
    ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
             [
                'attribute'=>'program.name',
                'format' => 'raw',
                'value'=> Html::a($model->program->name, Url::to(['/programs/view', 'id' => $model->program->id]), ['class' => 'blue', 'target' => '_blank']),
            ],
            'address',
            'schedule',
            'datestart:date',
            'datestop:date',
             [
                'label' => 'Обучающихся',
                'value'=> $contract1,
            ],
            [
                'label' => 'Заявок',
                'value'=> $contract2,
            ],
            [
                'label' => 'Мест',
                'value'=>$years['maxchild'] - $contract3,
            ],
        ],
    ]) ?>

    <?php
    if ($ContractsProvider->getTotalCount() > 0) {
        
      // return var_dump($contracts);
        
       
          echo GridView::widget([
            'dataProvider' => $ContractsProvider,
            'summary' => false,
              'columns' => [
                  'certificate.number',
                  'certificate.fio_child',
                  'date:date',
                  'number',
                  'start_edu_contract:date',
                  ['class' => 'yii\grid\ActionColumn',
                    'template' => '{newgroup}',
                    'buttons' =>
                         [
                             'newgroup' => function ($url, $model) {
                                 return Html::a('Сменить группу', Url::to(['/contracts/newgroup', 'id' => $model->id]), ['class' => 'btn btn-primary']);
                             },
                         ]
                 ],
              ],
            ]);
         
        
         /* 
        foreach ($contracts as $value) {
            
            
            echo DetailView::widget([
                'model' => $value,
                'attributes' => [
                    //'id',
                    //'date',
                    'certificate.fio_child',
                    'certificate.number',
                    'number',
                    //'program.name',
                    //'organization_id',
                    //'status',
                    //'status_termination',
                    //'status_comment:ntext',
                    //'status_year',
                    //'link_doc',
                    //'link_ofer',
                    //'group_id',
                    //'all_funds',
                    'funds_cert_1',
                    'funds_cert_2',
                    'funds_cert_3',
                    'funds_cert_4',
                    'funds_cert_5',
                    'funds_cert_6',
                    'funds_cert_7',
                    'funds_cert_8',
                    'funds_cert_9',
                    'funds_cert_10',
                    'funds_cert_11',
                    'funds_cert_12',
                    'funds_1',
                    'funds_2',
                    'funds_3',
                    'funds_4',
                    'funds_5',
                    'funds_6',
                    'funds_7',
                    'funds_8',
                    'funds_9',
                    'funds_10',
                    'funds_11',
                    'funds_12',
                    'all_parents_funds',
                    'parents_funds_1',
                    'parents_funds_2',
                    'parents_funds_3',
                    'parents_funds_4',
                    'parents_funds_5',
                    'parents_funds_6',
                    'parents_funds_7',
                    'parents_funds_8',
                    'parents_funds_9',
                    'parents_funds_10',
                    'parents_funds_11',
                    'parents_funds_12', 
                    //'start_edu_programm',
                    //'funds_gone',
                    //'stop_edu_contract',
                    //'start_edu_contract',
                ],
            ]);
        } */ 
        $del = 0;
    } else {
        echo "<h3>В этой группе нет обучающихся</h3>";
        $del = 1;
    } 
    ?>

    <?= Html::a('Назад', '/personal/organization-groups', ['class' => 'btn btn-primary']) ?>
    &nbsp;
    <?= Html::a('Редактировать', ['/groups/update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    &nbsp;
    <?php
    if ($del == 1) {
    echo '<div class="pull-right">';
       echo Html::a('Удалить', ['/groups/delete', 'id' => $model->id], ['class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить эту группу?',
                'method' => 'post']]);
         echo '</div>';
    } 
    ?>
</div>
