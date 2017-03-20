<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Mun;



$this->title = 'Программы';
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
        'dataProvider' => $ProgramsProvider,
        'filterModel' => $searchPrograms,
        'resizableColumns' => true,
            'pjax'=>true,
            'summary' => false,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'organization_id',
                //'verification',
                /*[
                    'class'=>'kartik\grid\ExpandRowColumn',
                    'width'=>'50px',
                    'value'=>function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detail'=>function ($model, $key, $index, $column) {
                        $searchModel = new ProgrammeModuleSearch();
                        $searchModel->program_id = $model->id;
                        //$searchModel->open = 1;
                        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                    
                         return Yii::$app->controller->renderPartial('/years/operator', ['searchModel'=>$searchModel, 'dataProvider'=>$dataProvider]);
                    },
                    'headerOptions'=>['class'=>'kartik-sheet-style'], 
                    'expandOneOnly'=>true
                ], */
                [
                    'attribute'=>'name',
                    'label' => 'Наименование',
                ],
                [
                    'attribute'=>'year',
                    'value'=> function ($data) {
                         if ($data->year == 1) { return 'Однолетняя';}
                        if ($data->year == 2) { return 'Двулетняя';}
                        if ($data->year == 3) { return 'Трехлетняя';}
                        if ($data->year == 4) { return 'Четырехлетняя';}
                        if ($data->year == 5) { return 'Пятилетняя';}
                        if ($data->year == 6) { return 'Шестилетняя';}
                        if ($data->year == 7) { return 'Семилетняя';}
                    }
                    
                ],
                [
                     'attribute' => 'directivity',
                     'label' => 'Направленность',
                 ],
                
                [
                     'attribute' => 'zab',
                     'label' => 'Категория детей',
                    'value'=> function ($data) {
                         $zab = explode(',', $data->zab);
                        $display = '';
                        foreach ($zab as $value) {
                            if ($value == 1 ) { $display = $display.', глухие';}
                            if ($value == 2 ) { $display = $display.', слабослышащие и позднооглохшие';}
                            if ($value == 3 ) { $display = $display.', слепые';}
                            if ($value == 4 ) { $display = $display.', слабовидящие';}
                            if ($value == 5 ) { $display = $display.', нарушения речи';}
                            if ($value == 6 ) { $display = $display.', фонетико-фонематическое нарушение речи';}
                            if ($value == 7 ) { $display = $display.', нарушение опорно-двигательного аппарата';}
                            if ($value == 8 ) { $display = $display.', задержка психического развития';}
                            if ($value == 9 ) { $display = $display.', расстройство аутистического спектра';}
                            if ($value == 10 ) { $display = $display.', нарушение интеллекта';}
                        }
                        if ($display == '') {
                            return 'без ОВЗ';
                        } 
                        else {
                         return mb_substr($display, 2);   
                        }
                         
                    }
                 ],
                [
                     'attribute' => 'age_group_min',
                     'label' => 'Возраст от',
                 ],
                [
                     'attribute' => 'age_group_max',
                     'label' => 'Возраст до',
                 ],

                [
                     'attribute' => 'rating',
                     'label' => 'Рейтинг',
                 ],
                [
                     'attribute' => 'limit',
                     'label' => 'Лимит',
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
                    'attribute'=>'mun',
                     'label' => 'Муниципалитет',
                    'filter'=>ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
                     'value' => function ($data) { 
                        $mun = (new \yii\db\Query())
                            ->select(['name'])
                            ->from('mun')
                            ->where(['id' => $data->mun])
                            ->one();
                         return $mun['name'];
                     },
                ],
                //'normative_price',
                
                 //'price',
                 //'rating',
                 //'limit',
                 //'study',
                // 'open',
                // 'goal:ntext',
                // 'task:ntext',
                // 'annotation:ntext',
                // 'hours',
                // 'ovz',
                // 'quality_control',
                 //'link',
                /* [
                    'attribute'=>'link',
                    'format' => 'raw',
                    'value' => function($data){
                        return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', '/'.$data->link );
                    }
                ], */
                // 'certification_date',
                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'programs',
                    'template' => '{view}',
                 ],
            ],
        ]); ?>
