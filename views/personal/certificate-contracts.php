<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Действующие договоры';
   $this->params['breadcrumbs'][] = $this->title;

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
                             ]
                     ],
                ],
            ]); ?>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>
<?php } */ ?>

<!--<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Действующие</a></li>
    <li><a data-toggle="tab" href="#panel4">Отказано</a></li>
</ul>
<br> -->

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <?= GridView::widget([
            'dataProvider' => $Contracts1Provider,
            'filterModel' => $searchContracts1,
            'summary' => false,
            'rowOptions' => function ($model, $index, $widget, $grid){
                  if($model->wait_termnate == 1){
                    return ['class' => 'danger'];
                  }
            },
            'columns' => [
             //['class' => 'yii\grid\SerialColumn'],
[
                    'attribute' => 'number',
                    'label' => 'Номер',
                ],
                [
                    'attribute' => 'date',
                    'format' => 'date',
                    'label' => 'Дата',
                ],            
                [
                    'attribute' => 'program',
                    'label'     => 'Программа',
                    'format'    => 'raw',
                    'value'     => function ($model)
                    {
                        /** @var $model app\models\Contracts */
                        return Html::a($model->program->name, Url::to(['/programs/view', 'id' => $model->program_id]), ['class' => 'blue', 'target' => '_blank']);
                    },
                ], 
                
                //'status_termination',
                //'status_comment:ntext',
                //'status_year',
                [
                    'attribute' => 'organization',
                    'label'     => 'Организация',
                    'format'    => 'raw',
                    'value'     => function ($model)
                    {
                        /** @var $model app\models\Contracts */
                        return Html::a($model->organization->name, Url::to(['/organization/view', 'id' => $model->organization_id]), ['class' => 'blue', 'target' => '_blank']);
                    },
                ],         
                [
                    'attribute' => 'rezerv',
                    'label' => 'Резерв',
                ],
                [
                    'attribute' => 'paid',
                    'label' => 'Списано',
                ],
                [
                    'attribute' => 'start_edu_contract',
                    'format' => 'date',
                    'label' => 'Начало обучения',
                ],
                [
                    'attribute' => 'stop_edu_contract',
                    'format' => 'date',
                    'label' => 'Конец обучения',
                ],
                 //'link_doc',
                 //'link_ofer',
                // 'start_edu_programm',
                // 'start_edu_contract',
                // 'stop_edu_contract',

                ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                 'controller' => 'contracts',
                ],
            ],
        ]); ?>
    </div>
  <!--  <div id="panel2" class="tab-pane fade">
        <?php /* GridView::widget([
            'dataProvider' => $Contracts3Provider,
            'filterModel' => $search3Contracts,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                'number',
                'date',
                'status',
                //'status_termination',
                //'status_comment:ntext',
                //'status_year',
                 //'link_doc',
                 //'link_ofer',
                [
                    'attribute'=>'link_doc',
                    'format'=>'raw',
                    'value'=> function($data){
                        return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', Url::to(['/contracts/mpdf', 'id' => $data->id]));
                    },
                ],
                // 'start_edu_programm',
                // 'start_edu_contract',
                // 'stop_edu_contract',
            ],
        ]); */ ?>
    </div>
    <div id="panel3" class="tab-pane fade">
        <?php /* GridView::widget([
            'dataProvider' => $Contracts0Provider,
            'filterModel' => $searchContracts0,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                'number',
                'date',
                'status',
                //'status_termination',
                //'status_comment:ntext',
                //'status_year',
                 'link_doc',
                 'link_ofer',
                // 'start_edu_programm',
                // 'start_edu_contract',
                // 'stop_edu_contract',

                ['class' => 'yii\grid\ActionColumn',
                'controller' => 'contracts',
                'template' => '{view} {update} {terminate}',
                 'buttons' =>
                     [
                         'terminate' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/disputes/terminate', 'id' => $model->id]), [
                                 'title' => Yii::t('yii', 'Расторгнуть контракт')
                             ]); },
                     ]
                ],
            ],
        ]); */ ?>
    </div> 
    <div id="panel4" class="tab-pane fade">
        <? /* GridView::widget([
            'dataProvider' => $ContractsNoProvider,
            'filterModel' => $searchNoContracts,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                //'id',
                'organization.name',
                    'program.name',
                    'year.year',
                    'group.name',
                    

                ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                 'controller' => 'contracts',
                ],
            ]
        ]);  */ ?>
    </div>--> 
</div>

