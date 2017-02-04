<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;


$this->title = 'Предварительные записи';
   $this->params['breadcrumbs'][] = 'Предварительные записи';
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

<?= GridView::widget([
    'dataProvider' => $FavoritesProvider,
    'filterModel' => $searchFavorites,
    'columns' => [
        //['class' => 'yii\grid\SerialColumn'],

        //'id',
        'certificate.fio_child',
        'certificate.number',
        [
            'attribute' => 'program.name',
            'format' => 'raw',
            'value'=> function($data){

            return Html::a($data->program->name, Url::to(['/programs/view', 'id' => $data->program->id]), ['class' => 'blue', 'target' => '_blank']);
            },
        ],
        'year.year',
       // 'organization_id',

        ['class' => 'yii\grid\ActionColumn',
         'controller' => 'certificates',
         'template' => '{view}',
         'buttons' => [
            'view' => function ($url, $model) {
                     return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/certificates/view', 'id' => $model->certificate->id]));  
                //return var_dump($model);
                },
            ],
        ],
    ],
]); ?>
