<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Organization;


$this->title = 'Счета';
   $this->params['breadcrumbs'][] = 'Счета';
/* @var $this yii\web\View */
?>

<?php /* if ($informsProvider->getTotalCount() > 0) { ?>
    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Оповещения</h4>
          </div>
          <div class="modal-body">
            <?= GridView::widget([
                'dataProvider' => $informsProvider,
                'summary' => false,
                'showHeader' => false,
                'columns' => [
                    // 'id',
                    // 'contract_id',
                    // 'from',
                    'date',
                    'text:ntext',
                    'program_id',
                    // 'read',

                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{permit} {view}',
                         'buttons' =>
                             [
                                 'permit' => function ($url, $model) {
                                     return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['/informs/read', 'id' => $model->id]), [
                                         'title' => Yii::t('yii', 'Отметить как прочитанное'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top'
                                     ]); },
                                'view' => function ($url, $model) {
                                     return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/programs/view', 'id' => $model->program_id]), [
                                         'title' => Yii::t('yii', 'Просмотреть программу'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top'
                                     ]); },
                             ]
                     ],
                ],
            ]); ?>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>
<?php } */ ?>

<?php
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
    $organizations = new Organization();
    $organization = $organizations->getOrganization();
    if ($roles['organizations'] and $organization['actual'] != 0) {
        
            $rows = (new \yii\db\Query())
                ->select(['payer_id'])
                ->from('cooperate')
                ->where(['organization_id' => $organization['id']])
                ->andWhere(['status' => 1])
                ->column();
            
            $preinvoice = array();
            foreach ($rows as $payer_id) {
                $payer = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('invoices')
                    ->where(['organization_id' => $organization['id']])
                    ->andWhere(['payers_id' => $payer_id])
                    ->andWhere(['month' => date('m')])
                    ->andWhere(['prepayment' => 1])
                    ->andWhere(['status' => [0,1,2]])
                    ->column();
                
                if (!$payer) {
                    array_push($preinvoice, $payer_id);
                }
            }
        
            $invoice = array();
            foreach ($rows as $payer_id) {
                $payer2 = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('invoices')
                    ->where(['organization_id' => $organization['id']])
                    ->andWhere(['payers_id' => $payer_id])
                    ->andWhere(['month' => date('m')-1])
                    ->andWhere(['prepayment' => 0])
                    ->andWhere(['status' => [0,1,2]])
                    ->column();
                
                if (!$payer2) {
                    array_push($invoice, $payer_id);
                }
            }
        
            if (date('m') == 12) { 
            $dec = array();
            foreach ($rows as $payer_id) {
                $payer3 = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('invoices')
                    ->where(['organization_id' => $organization['id']])
                    ->andWhere(['payers_id' => $payer_id])
                    ->andWhere(['month' => 12])
                    ->andWhere(['prepayment' => 0])
                    ->andWhere(['status' => [0,1,2]])
                    ->column();
                
                if (!$payer3) {
                    array_push($dec, $payer_id);
                }
            }
            }

        
        $date_last=explode(".", date("d.m.Y"));
            switch ($date_last[1] - 1){
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
        
        $date_now=explode(".", date("d.m.Y"));
            switch ($date_now[1]){
            case 1: $m_now='январь'; break;
            case 2: $m_now='февраль'; break;
            case 3: $m_now='март'; break;
            case 4: $m_now='апрель'; break;
            case 5: $m_now='май'; break;
            case 6: $m_now='июнь'; break;
            case 7: $m_now='июль'; break;
            case 8: $m_now='август'; break;
            case 9: $m_now='сентябрь'; break;
            case 10: $m_now='октябрь'; break;
            case 11: $m_now='ноябрь'; break;
            case 12: $m_now='декабрь'; break;
            }
            
        
        echo "<p>";
        if ($invoice && date('m') != 1) {
            echo Html::a('Создать счет за '.$m , ['groups/invoice'], ['class' => 'btn btn-success']);
        }
        if ($preinvoice) {
            echo "&nbsp;";
            echo Html::a('Создать аванс за '.$m_now , ['groups/preinvoice'], ['class' => 'btn btn-success']);
        }
        if (!empty($dec)) {
            echo Html::a('Создать счет за декабрь', ['groups/dec'], ['class' => 'btn btn-warning pull-right']);
            echo "<br>";
            echo "<br>";
        }
        echo "</p>";
    }
    ?>
<?= GridView::widget([
    'dataProvider' => $InvoicesProvider,
    'filterModel' => $searchInvoices,
    'summary' => false,
    'columns' => [
        //['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'contracts',
            'number',
            'date:date',
            [
                    'attribute'=>'month',
                    'label' => 'Месяц',
                    'value' => function($data){
                        switch ($data->month){
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
                        return $m;
                    }
            ],
            [
                    'attribute' => 'payers',
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
                    'attribute'=>'prepayment',
                    'label' => 'Тип',
                    'format' => 'raw',
                    'value' => function($data){
                        return $data->prepayment == 1 ? 'Аванс' : 'Счёт';
                    }
                ],
        
            [
                    'attribute'=>'status',
                    'format' => 'raw',
                    'value' => function($data){
                        if ($data->status == 0) {return 'Не просмотрен';}
                        if ($data->status == 1) {return 'В работе';}
                        if ($data->status == 2) {return 'Оплачен';}
                        if ($data->status == 3) {return 'Удален';}
                    }
                ],
            [
                    'attribute'=>'link',
                    'label' => 'Скачать',
                    'format' => 'raw',
                    'value' => function($data){
                        if ($data->prepayment == 1) {
                            return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', Url::to(['/invoices/mpdf', 'id' => $data->id]));
                        } else {
                            return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', Url::to(['/invoices/invoice', 'id' => $data->id]));
                        }
                    }
                ],
            //'status',
            //'status_termination',
            // 'status_comment:ntext',
            // 'status_year',
            // 'link_doc',
            // 'link_ofer',
            // 'start_edu_programm',
            // 'start_edu_contract',
            // 'stop_edu_contract',

        ['class' => 'yii\grid\ActionColumn',
         'controller' => 'invoices',
         'template' => '{view}',
        ],
    ],
]); ?>
