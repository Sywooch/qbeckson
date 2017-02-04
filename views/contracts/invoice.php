<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use yii\grid\GridView;
use app\models\Organization;
use app\models\Contracts;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvoicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$date=explode(".", date("d.m.Y"));
            switch ($date[1] - 1){
            case 1: $m='январе'; break;
            case 2: $m='феврале'; break;
            case 3: $m='марте'; break;
            case 4: $m='апреле'; break;
            case 5: $m='мае'; break;
            case 6: $m='июне'; break;
            case 7: $m='июле'; break;
            case 8: $m='августе'; break;
            case 9: $m='сентябре'; break;
            case 10: $m='октябре'; break;
            case 11: $m='ноябре'; break;
            case 12: $m='декабре'; break;
            }

$this->title = 'Счет будет выставлен по следующим договорам:';
  $this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
  $this->params['breadcrumbs'][] = ['label' => 'Полнота оказанных услуг в '.$m , 'url' => ['/groups/invoice']];
$this->params['breadcrumbs'][] = ['label' => 'Выберите плательщика', 'url' => ['/contracts/invoice']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoices-index">
    
    <?php
    $organizations = new Organization();
        $organization = $organizations->getOrganization();
    
    $lmonth = date('m')-1;
            $start = date('Y').'-'.$lmonth.'-01';
            
            $cal_days_in_month = cal_days_in_month(CAL_GREGORIAN, $lmonth, date('Y'));
            
            $stop = date('Y').'-'.$lmonth.'-'.$cal_days_in_month;
            
            //return var_dump($payer);
            $contracts_all = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['<=', 'start_edu_contract', $start])
                ->andWhere(['>=', 'stop_edu_contract', $start])
                ->andWhere(['organization_id' => $organization->id])
                ->andWhere(['payer_id' => $payers])
                ->andWhere(['status' => 1])
                ->andWhere(['>', 'all_funds', 0])
                ->column();
                            
            $contracts_terminated = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['<=', 'start_edu_contract', $start])
                ->andWhere(['>=', 'stop_edu_contract', $start])
                ->andWhere(['organization_id' => $organization->id])
                ->andWhere(['payer_id' => $payers])
                ->andWhere(['status' => 4])
                ->andWhere(['<=' ,'date_termnate', $stop])
                ->andWhere(['>=' ,'date_termnate', $start])
                ->andWhere(['>', 'all_funds', 0])
                ->column();
            
            //array_push($contracts, $contracts_terminated);
       // $contracts += $contracts_terminated;
            $contracts = array_merge($contracts_all, $contracts_terminated);
    
        
            $sum = 0;
            foreach($contracts as $contract_id){           
                $contract = Contracts::findOne($contract_id);                     
                
                $completeness = (new \yii\db\Query())
                            ->select(['sum'])
                            ->from('completeness')
                            ->where(['contract_id' => $contract->id])
                            ->andWhere(['preinvoice' => 0])
                            ->andWhere(['month' => $lmonth])
                            ->one();
                
                /*
                $nopreinvoice = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('invoices')
                            ->where(['month' => date('m')])
                            ->andWhere(['prepayment' => 1])
                    ->andWhere(['status' => [0,1,2]])
                            ->one();
                    
                $precompleteness = (new \yii\db\Query())
                        ->select(['sum'])
                        ->from('completeness')
                        ->where(['contract_id' => $contract->id])
                        ->andWhere(['preinvoice' => 1])
                        ->andWhere(['month' => date('m')])
                        ->one();
                
                if (!isset($nopreinvoice['id']) or empty($nopreinvoice['id'])) {
                    $sum += $completeness['sum'] + $precompleteness['sum'];
                }
                else { */
                    $sum += $completeness['sum'];
              // }
                
            }
    
    echo '<h1>Всего необходимо для оплаты договоров - '.round($sum, 2).' руб.</h1>';
    ?>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=Html::beginForm(['invoices/new', 'payer' => $payers['payer_id']],'post');?>

    <?= GridView::widget([
        'options' => ['id' => 'invoices'],
        'dataProvider' => $ContractsProvider,
        //'filterModel' => $searchContracts,
        'summary' => false,
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
                            ->andWhere(['month' => date('m')-1])
                            ->andWhere(['preinvoice' => 0])
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
                            ->andWhere(['month' => date('m')-1])
                            ->andWhere(['preinvoice' => 0])
                            ->one();
                        /*
                         $nopreinvoice = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('invoices')
                            ->where(['month' => date('m')])
                            ->andWhere(['prepayment' => 1])
                             ->andWhere(['status' => [0,1,2]])
                            ->one();
                        
                       $precompleteness = (new \yii\db\Query())
                                ->select(['sum'])
                                ->from('completeness')
                                ->where(['contract_id' => $model->id])
                                ->andWhere(['preinvoice' => 1])
                                ->andWhere(['month' => date('m')])
                                ->one();

                        if (!isset($nopreinvoice['id']) or empty($nopreinvoice['id'])) {
                            return round($completeness['sum'] + $precompleteness['sum'], 2);
                        }
                        else { */
                            return round($completeness['sum'], 2);
                       // }
                    }
            ],
            //'payer_id',
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
    <?= Html::a('Назад', ['/contracts/invoice'], ['class' => 'btn btn-primary']) ?>
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
