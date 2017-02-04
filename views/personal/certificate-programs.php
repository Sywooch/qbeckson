<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

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
    <li class="active"><a data-toggle="tab" href="#panel1">Обучение в текущем году</a></li>
    <li><a data-toggle="tab" href="#panel2">Ожидающие подтверждения <span class="badge"><?= $programnocertProvider->getTotalCount() ?></span></a></li>
</ul>
<br>

<div class="tab-content">
    <p>
        <?= Html::a('Поиск программы', ['programs/index'], ['class' => 'btn btn-success']) ?>
    </p>
    <div id="panel1" class="tab-pane fade in active">
        
        <?= GridView::widget([
            'dataProvider' => $programcertProvider,
            'filterModel' => $programcertModel,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'program_id',
                //'organization_id',
                //'verification',
                'organization.name',
                 'name',
                'year',
                 //'open',
                // 'normative_price',
                 //'price',
                 //'rating',
                 //'limit',
                // 'study',
                // 'open',
                // 'goal:ntext',
                // 'task:ntext',
                // 'annotation:ntext',
                // 'hours',
                // 'ovz',
                // 'quality_control',
                // 'link',
                // 'certification_date',

                ['class' => 'yii\grid\ActionColumn',
                'controller' => 'programs',
                'template' => '{view}',
                ],
            ],
        ]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $programnocertProvider,
            'filterModel' => $programnocertModel,
            'columns' => [
               //['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'program_id',
                //'organization_id',
                //'verification',
                'organization.name',
                 'name',
                'year',
                 //'open',
                // 'normative_price',
                 //'price',
                 //'rating',
                 //'limit',
                // 'study',
                // 'open',
                // 'goal:ntext',
                // 'task:ntext',
                // 'annotation:ntext',
                // 'hours',
                // 'ovz',
                // 'quality_control',
                // 'link',
                // 'certification_date',

                ['class' => 'yii\grid\ActionColumn',
                'controller' => 'programs',
                'template' => '{view}',
                ],
            ],
        ]); ?>
    </div>
</div>
