<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;


$this->title = 'Сертификаты';
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
<?php }  ?>


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

<div class="pull-right">
    <?= Html::a('Обновить номиналы', Url::to(['/certificates/allnominal', 'id' => $payer_id]), ['class' => 'btn btn-success']) ?>
</div>

<p>
    <?= Html::a('Добавить сертификат', ['certificates/create'], ['class' => 'btn btn-success']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $CertificatesProvider,
    'filterModel' => $searchCertificates,
    'pjax'=>true,
        'summary' => false,
        'summary' => false,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute'=> 'number',
                    'label'=> 'Номер',
                ],
                'fio_child',
                [
                    'attribute'=> 'nominal',
                    'label'=> 'Номинал',
                ],
                [
                    'attribute'=> 'rezerv',
                    'label'=> 'Резерв',
                ],
                [
                    'attribute'=> 'balance',
                    'label'=> 'Остаток',
                ],
                [
                    'label'=> 'Договоров',
                    'value' => function($data){
                             $previus = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('contracts')
                            ->where(['certificate_id' => $data->id])
                            ->andWhere(['status' => 1])
                            ->count();
                        
                        return $previus;
                    }
                ],
                ['attribute'=>'actual',
                      'value' => function($data){
                             if ($data->actual == 0) {
                                return '-';
                             } else {
                                return '+';
                            }
                      }
                    ],

            ['class' => 'yii\grid\ActionColumn',
                'controller' => 'certificates',
                'template' => '{view}',
            ],
        ],
]); ?>
