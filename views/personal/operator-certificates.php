<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\Informs;
use yii\helpers\Url;
use kartik\export\ExportMenu;
//use kartik\grid\GridView;

$this->title = 'Сертификаты';
   $this->params['breadcrumbs'][] = 'Сертификаты';
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

<?= ExportMenu::widget([
    'dataProvider' => $CertificatesExportProvider,
    'target' => '_self',
    'columns' => [
                'id',
                'user_id',
                [
                    'attribute'=> 'number',
                    'label'=> 'Номер',
                ],
                'fio_child',
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
    ],
]); ?>
