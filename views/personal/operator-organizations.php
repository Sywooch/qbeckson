<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\Informs;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
//use kartik\grid\GridView;

/* @var $this yii\web\View */
$this->title = 'Организации';
   $this->params['breadcrumbs'][] = 'Организации';
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


    <?= Html::a('Добавить организацию', ['organization/create'], ['class' => 'btn btn-success']) ?>
    <div class="pull-right">
        <?= Html::a('Пересчитать лимиты', ['organization/alllimit'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Пересчитать рейтинги', ['organization/allraiting'], ['class' => 'btn btn-primary']) ?>
    </div>
    <br><br>


<?= GridView::widget([
    'dataProvider' => $OrganizationProvider,
    'filterModel' => $searchOrganization,
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
        [
            'attribute'=>'orgtype',
            'label' => 'Тип организации',
            'filter' => [1 => 'Образовательная организация',
                2 => 'Организация, осуществляющая обучение',
                3 => 'Индивидуальный предприниматель, оказывающий услуги с наймом работников',
                4 => 'Индивидуальный предприниматель, оказывающий услуги без найма работников'
            ],
        ],
        [
            'label' => 'Число программ',
            'attribute'=>'certprogram',
        ],
        'max_child',
        [
            'label' => 'Число обучающихся',
            'value' => function($data){
                $cert = (new \yii\db\Query())
                        ->select(['certificate_id'])
                        ->from('contracts')
                        ->where(['organization_id' => $data->id])
                        ->andWhere(['status' => 1])
                        ->column();
                $cert = array_unique($cert);
                $cert = count($cert);
            
            return $cert;
            }
        ],
        'amount_child',
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

<?= ExportMenu::widget([
    'dataProvider' => $OrganizationProvider,
    'target' => '_self',
    'columns' => [
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
            'attribute' => 'certprogram',
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
                $cert = (new \yii\db\Query())
                        ->select(['certificate_id'])
                        ->from('contracts')
                        ->where(['organization_id' => $data->id])
                        ->andWhere(['status' => 1])
                        ->all();
                $cert = array_unique($cert);
                $cert = count($cert);
            
            return $cert;
            }
        ],
        'amount_child',
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
    ],
]); ?>
