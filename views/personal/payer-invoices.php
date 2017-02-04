<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;


$this->title = 'Счета';
   $this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */
?>

<?php /* if ($InformsProvider->getTotalCount() > 0) { ?>
    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Оповещения</h4>
          </div>
          <div class="modal-body">
            <?= GridView::widget([
                'dataProvider' => $InformsProvider,
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
                             ]
                     ],
                ],
            ]); ?>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>
<?php } ?>


<?php if ($CooperateProvider->getTotalCount() > 0) { ?>
    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Новые организации</h4>
          </div>
          <div class="modal-body">
           <p>Эти организации желают с вами сотрудничать</p>
            <?= GridView::widget([
                'dataProvider' => $CooperateProvider,
                'summary' => false,
                'showHeader' => false,
                'columns' => [
                     'organization_id',

                    ['class' => 'yii\grid\ActionColumn',
                        'controller' => 'cooperate',
                        'template' => '{view} {read}',
                         'buttons' =>
                             [
                                 'permit' => function ($url, $model) {
                                     return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['/payers/cooperateok', 'id' => $model->id]), [
                                         'title' => Yii::t('yii', 'Одобрить'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top'
                                     ]); },

                                'terminate' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::to(['/payers/cooperateno', 'id' => $model->id]), [
                                         'title' => Yii::t('yii', 'Отказать'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top'
                                     ]); },

                                'read' => function ($url, $model) {
                                     return Html::a('<span class="glyphicon glyphicon-check"></span>', Url::to(['/cooperate/read', 'id' => $model->id]), [
                                         'title' => Yii::t('yii', 'Отметить как прочитанное'),
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

<?= GridView::widget([
    'dataProvider' => $InvoicesProvider,
    'filterModel' => $searchInvoices,
    'columns' => [
        //['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'contract_id',
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
