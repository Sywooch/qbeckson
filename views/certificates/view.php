<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Certificates */

$this->title = $model->number;

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if (isset($roles['operators'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['/personal/operator-certificates']];
}
if (isset($roles['payer'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['/personal/payer-certificates']];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificates-view col-md-8 col-md-offset-2">

    <h1><?= Html::encode($this->title) ?></h1>
    
     <?php
       $contracts = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['certificate_id' => $model->id])
            ->andWhere(['status' => 1])
            ->count();
    
         
        
        if (isset($roles['payer'])) {
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'fio_child',
                    'fio_parent',
                    [
                        'attribute'=>'actual',
                        'value'=>$model->actual == 1 ? 'Активен' : 'Приостановлен',
                    ],
                ],
            ]);
        }
        else {
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'fio_child',
                    'fio_parent',
                    [
                        'label'=>'Плательщик',
                        'format' => 'raw',
                        'value'=> Html::a($model->payer->name, Url::to(['/payers/view', 'id' => $model->payer_id]), ['class' => 'blue']),
                    ],
                    [
                        'attribute'=>'actual',
                        'value'=>$model->actual == 1 ? 'Активен' : 'Приостановлен',
                    ],
                ],
            ]);
        }
    ?>
    
    <?php
     if (isset($roles['operators']) || isset($roles['payer'])) {
         
         if (isset($roles['operators'])) {
             $link = '/personal/operator-contracts';
         }
         
         if (isset($roles['payer'])) {
             $link = '/personal/payer-contracts';
         }
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'nominal',
            'certGroup.group',
            'rezerv',
            'balance',
            [
                'label'=> Html::a('Число заключенных договоров', Url::to([$link, 'cert' => $model->number]), ['class' => 'blue', 'target' => '_blank']),
                'value' => $contracts,
            ],
        ],
    ]); 
     }
    ?>
    <p>
        <?php if (isset($roles['operators'])) {
            echo Html::a('Назад', '/personal/operator-certificates', ['class' => 'btn btn-primary']);
        }
        if (isset($roles['organizations'])) {
            echo Html::a('Назад', '/personal/organization-favorites', ['class' => 'btn btn-primary']);
        }
        if (isset($roles['payer'])) {
            echo '<div class="pull-right">';
            if ($model->actual == 0) {
                
                echo Html::a('Активировать', Url::to(['/certificates/actual', 'id' => $model->id]), ['class' => 'btn btn-success']);
                echo '&nbsp;';
             } else {
                
                echo Html::a('Заморозить', Url::to(['/certificates/noactual', 'id' => $model->id]), ['class' => 'btn btn-danger']);
                echo '&nbsp;';
             }
            echo Html::a('Удалить', Url::to(['/certificates/delete', 'id' => $model->id]), ['class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post']]);
            echo '</div>';
            
            echo Html::a('Назад', '/personal/payer-certificates', ['class' => 'btn btn-primary']);
            echo '&nbsp;';
            echo Html::a('Редактировать', Url::to(['/certificates/update', 'id' => $model->id]), ['class' => 'btn btn-primary']);
            echo '&nbsp;';
            
            
        }

        ?>
    </p>
</div>
