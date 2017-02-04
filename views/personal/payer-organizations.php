<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use app\models\Payers;


$this->title = 'Организации';
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

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Действующие</a></li>
    <li><a data-toggle="tab" href="#panel2">Ожидающие подтверждения <span class="badge"><?= $Organization0Provider->getTotalCount() ?></span></a></li>
</ul>
<br>

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <?= GridView::widget([
            'dataProvider' => $Organization1Provider,
            'filterModel' => $searchOrganization1,
            'pjax'=>true,
    'summary' => false,
    'columns' => [
        //['class' => 'yii\grid\SerialColumn'],

        //'id',
        //'user_id',
      /*  ['attribute'=>'actual',
                  'format' => 'raw',
                  'value' => function($data){
                         if ($data->actual == 0) {
                            return Html::a('Разрешить деятельность', Url::to(['/organization/actual', 'id' => $data->id]), ['class' => 'btn btn-success']);
                         } else {
                        $previus = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('contracts')
                            ->where(['organization_id' => $data->id])
                            ->andWhere(['status' => 1])
                            ->count();
                            if (!$previus) {
                                return Html::a('Приостановить', Url::to(['/organization/noactual', 'id' => $data->id]), ['class' => 'btn btn-danger']);
                            }
                            else {
                                return '';
                            }
                        }
                  }
        ], */
        'name',
        //'type',
        ['attribute'=>'type',
            'value' => function($data){
                if ($data->type == 1) { 
                    return 'Образовательная организация';
                }
                if ($data->type == 2) { 
                    return 'Организация, осуществляющая обучение';
                }
                if ($data->type == 3) { 
                    return 'Индивидуальный предприниматель (с наймом)';
                }
                if ($data->type == 4) { 
                    return 'Индивидуальный предприниматель (без найма)';
                }
            }
        ],
        [
            'label' => 'Число программ',
            'value' => function($data){
            $programs = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('programs')
                        ->where(['organization_id' => $data->id])
                        ->andWhere(['verification' => 2])
                        ->count();
            
            return $programs;
            }
        ],
        //'license_date',
        //'license_number',
         //'license_issued',
        // 'requisites',
        // 'representative',
        //'address_legal',
        //'geocode',
        'max_child',
        [
            'label' => 'Число обучающихся',
            'value' => function($data){
                 $payers = new Payers();
                $payer = $payers->getPayer();
                
                $cert = (new \yii\db\Query())
                        ->select(['certificate_id'])
                        ->from('contracts')
                        ->where(['organization_id' => $data->id])
                        ->andWhere(['status' => 1])
                        ->andWhere(['payer_id' => $payer])
                        ->column();

                $cert = array_unique($cert);
                $cert = count($cert);
            
            return $cert;
            }
        ],
        //'amount_child',
        [
            'label' => 'Число договоров',
            'value' => function($data){ 
                $payers = new Payers();
                $payer = $payers->getPayer();
                
                $cert = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('contracts')
                        ->where(['organization_id' => $data->id])
                        ->andWhere(['status' => 1])
                        ->andWhere(['payer_id' => $payer])
                        ->column();
                $cert = array_unique($cert);
                $cert = count($cert);
            
            return $cert;
            }
        ],
        //'inn',
        //'okopo',
        'raiting',
        // 'ground',
        //'user.id',
        //'user.username',
        //'user.password',
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
            'controller' => 'organization',
         'template' => '{view}',
        ],
    ],
]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $Organization0Provider,
            'filterModel' => $searchOrganization0,
            'pjax'=>true,
    'summary' => false,
    'columns' => [
        //['class' => 'yii\grid\SerialColumn'],

        //'id',
        //'user_id',
      /*  ['attribute'=>'actual',
                  'format' => 'raw',
                  'value' => function($data){
                         if ($data->actual == 0) {
                            return Html::a('Разрешить деятельность', Url::to(['/organization/actual', 'id' => $data->id]), ['class' => 'btn btn-success']);
                         } else {
                        $previus = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('contracts')
                            ->where(['organization_id' => $data->id])
                            ->andWhere(['status' => 1])
                            ->count();
                            if (!$previus) {
                                return Html::a('Приостановить', Url::to(['/organization/noactual', 'id' => $data->id]), ['class' => 'btn btn-danger']);
                            }
                            else {
                                return '';
                            }
                        }
                  }
        ], */
        'name',
        //'type',
        ['attribute'=>'type',
            'value' => function($data){
                if ($data->type == 1) { 
                    return 'Образовательная организация';
                }
                if ($data->type == 2) { 
                    return 'Организация, осуществляющая обучение';
                }
                if ($data->type == 3) { 
                    return 'Индивидуальный предприниматель (с наймом)';
                }
                if ($data->type == 4) { 
                    return 'Индивидуальный предприниматель (без найма)';
                }
            }
        ],
        [
            'label' => 'Число программ',
            'value' => function($data){
            $programs = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('programs')
                        ->where(['organization_id' => $data->id])
                        ->andWhere(['verification' => 2])
                        ->count();
            
            return $programs;
            }
        ],
        //'license_date',
        //'license_number',
         //'license_issued',
        // 'requisites',
        // 'representative',
        //'address_legal',
        //'geocode',
        'max_child',
        [
            'label' => 'Число обучающихся',
            'value' => function($data){ 
                $payers = new Payers();
                $payer = $payers->getPayer();
                
                $cert = (new \yii\db\Query())
                        ->select(['certificate_id'])
                        ->from('contracts')
                        ->where(['organization_id' => $data->id])
                        ->andWhere(['status' => 1])
                        ->andWhere(['payer_id' => $payer])
                        ->all();
                $cert = array_unique($cert);
                $cert = count($cert);
            
            return $cert;
            }
        ],
        
        [
            'label' => 'Число договоров',
            'value' => function($data){ 
                $payers = new Payers();
                $payer = $payers->getPayer();
                
                $cert = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('contracts')
                        ->where(['organization_id' => $data->id])
                        ->andWhere(['status' => 1])
                        ->andWhere(['payer_id' => $payer])
                        ->all();
                $cert = array_unique($cert);
                $cert = count($cert);
            
            return $cert;
            }
        ],
        //'amount_child',
        //'inn',
        //'okopo',
        'raiting',
        // 'ground',
        //'user.id',
        //'user.username',
        //'user.password',
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
            'controller' => 'organization',
         'template' => '{view}',
        ],
    ],
]); ?>
    </div>
</div>
