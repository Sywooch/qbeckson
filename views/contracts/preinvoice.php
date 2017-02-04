<?php

use yii\helpers\Html;
//use kartik\grid\GridView;
use yii\grid\GridView;
use app\models\Organization;
use app\models\Contracts;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvoicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



$date=explode(".", date("d.m.Y"));
            switch ($date[1]){
            case 1: $m='январь'; break;
            case 2: $m='февраль'; break;
            case 3: $m='март'; break;
            case 4: $m='апрель'; break;
            case 5: $m='май'; break;
            case 6: $m='июнь'; break;
            case 7: $m='июль'; break;
            case 8: $m='август'; break;
            case 9: $m='сентябрь'; break;
            case 10: $m='октябрь'; break;
            case 11: $m='ноябрь'; break;
            case 12: $m='декабрь'; break;
            }

$this->title = 'Аванс будет выставлен по следующим договорам:';
  $this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
  $this->params['breadcrumbs'][] = ['label' => 'Авансировать за '.$m , 'url' => ['/groups/preinvoice']];
$this->params['breadcrumbs'][] = ['label' => 'Выберите плательщика', 'url' => ['/contracts/preinvoice']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoices-index">

   <?php
    $organizations = new Organization();
        $organization = $organizations->getOrganization();
    
    $month_start = date('Y-m-').'01';
    $contracts = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['<=', 'start_edu_contract', $month_start])
                ->andWhere(['>=', 'stop_edu_contract', $month_start])
                ->andWhere(['organization_id' => $organization->id])
                ->andWhere(['payer_id' => $payers->payer_id])
                ->andWhere(['status' => 1])
                ->andWhere(['>', 'all_funds', 0])
                ->column();
        
            $sum = 0;
            foreach($contracts as $contract_id){           
                $contract = Contracts::findOne($contract_id);                     
                
                $completeness = (new \yii\db\Query())
                            ->select(['sum'])
                            ->from('completeness')
                            ->where(['contract_id' => $contract->id])
                            ->andWhere(['preinvoice' => 1])
                            ->andWhere(['month' => date('m')])
                            ->one();
                
                $sum += $completeness['sum'];
            }
    
    echo '<h1>Всего необходимо для оплаты договоров - '.round($sum, 2).' руб.</h1>';
    ?>
   
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=Html::beginForm(['invoices/preinvoice', 'payer' => $payers['payer_id']],'post');?>

    <?= GridView::widget([
        'options' => ['id' => 'invoices'],
        'dataProvider' => $ContractsProvider,
        //'filterModel' => $searchContracts,
        //'showPageSummary' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            /*['class' => 'yii\grid\CheckboxColumn',
             'checkboxOptions' => function ($model, $key, $index, $column) {
                 return ['value' => $model->id];
             }   
            ], */

            //'id',
            'number',
            'date',
            'certificate.number',
            [
                    'label' => 'Процент',
                    'value' => function($model){
                        
                        $completeness = (new \yii\db\Query())
                            ->select(['completeness'])
                            ->from('completeness')
                            ->where(['contract_id' => $model->id])
                            ->andWhere(['month' => date('m')])
                            ->andWhere(['preinvoice' => 1])
                            ->one();
                        return $completeness['completeness'];
                    }
            ],
            [
                    'label' => 'К оплате',
                    'value' => function($model){
                        
                       $completeness = (new \yii\db\Query())
                            ->select(['sum'])
                            ->from('completeness')
                            ->where(['contract_id' => $model->id])
                            ->andWhere(['month' => date('m')])
                           ->andWhere(['preinvoice' => 1])
                            ->one();
                        return round($completeness['sum'], 2);
                    }
            ],
            //'status',
            //'status_termination',
            //'status_comment:ntext',
            //'status_year',
            // 'link_doc',
            // 'link_ofer',
            // 'start_edu_programm',
            // 'start_edu_contract',
            // 'stop_edu_contract',

           // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    
    <!-- <p id='total'>Плательщиков: 0</p> -->
<?= Html::a('Назад', ['/contracts/preinvoice'], ['class' => 'btn btn-primary']) ?>
&nbsp;
    <?=Html::submitButton('Продолжить', ['class' => 'btn btn-primary',]);?>
    
    
    <?php /* 
    $script = <<< JS
                $(document).ready(function(){
                    $("#invoices input[type=checkbox]").click(function(){
                                
                        var keys = $('#invoices').yiiGridView('getSelectedRows');
                        
                        //alert(keys);
                        
                        $.post('/invoices/countpayer',
                               {
                                   keylist : keys,
                               }, 
                                function(data) { var data = $.parseJSON(data); $('#total').text('Плательщиков: ' + data.total);   }
                        ); 
                    });
                });
JS;
$this->registerJs($script);
  */  ?>
    <?= Html::endForm();?>
</div>
