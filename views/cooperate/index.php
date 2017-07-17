<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\Mun;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CooperateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Соглашения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cooperate-index">
   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'number',
            [
              'attribute' => 'date',  
                'format' => 'date',
                'label' => 'Дата соглашения',
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
                    'attribute' => 'payers',
                    'label' => 'Плательщик',
                    'format' => 'raw',
                    'value'=> function($data){
                        
                        $payer = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('payers')
                            ->where(['name' => $data->payers->name])
                            ->one();
                        
                        
                    return Html::a($data->payers->name, Url::to(['/payers/view', 'id' => $payer['id']]), ['class' => 'blue', 'target' => '_blank']);
                    },
                    'label'=> 'Плательщик',
                ],
                [
              'attribute' => 'payersmun',  
                'label' => 'Район (округ)',
                'filter'=>ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
                 'value' => function ($data) { 
                    $mun = (new \yii\db\Query())
                        ->select(['name'])
                        ->from('mun')
                        ->where(['id' => $data->payers->mun])
                        ->one();
                     return $mun['name'];
                 },
            ],
            [  
                'label' => 'Число договоров',
                'format'=> 'raw',
                'value' => function ($data) { 
                    $contracts = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('contracts')
                        ->where(['payer_id' => $data->payers->id])
                        ->andWhere(['organization_id' => $data->organization->id])
                        ->count();
                    
                      return Html::a($contracts, Url::to(['/personal/operator-contracts', 'org' => $data->organization->name, 'payer' => $data->payers->name]), ['class' => 'blue', 'target' => '_blank']);
                 },
            ],
            
            // 'date_dissolution',
            // 'status',
            // 'reade',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
