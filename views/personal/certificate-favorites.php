<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Mun;
use app\models\Certificates;

/* @var $this yii\web\View */
?>

<?php  /* if ($informsProvider->getTotalCount() > 0) { ?>
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



<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
    <?= GridView::widget([
        'dataProvider' => $FavoritesProvider,
        'filterModel' => $searchFavorites,
        'pjax'=>true,
        'rowOptions' => function ($model, $index, $widget, $grid){
                  if($model){
                      
                      $certificates = new Certificates();
                            $certificate = $certificates->getCertificates();

                $rows = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('cooperate')
                    ->where(['payer_id'=> $certificate['payer_id']])
                    ->andWhere(['organization_id' => $model['organization_id']])
                    ->andWhere(['status'=> 1])
                    ->count();
                      
                      if ($rows == 0) {
                    return ['class' => 'danger'];
                          }
                      
                      $org = (new \yii\db\Query())
                        ->select(['actual'])
                        ->from('organization')
                        ->where(['id'=> $model['organization_id']])
                        ->one();
                      
                      if ($org['actual'] == 0) {
                        return ['class' => 'hide'];
                    }
                  }
            },
        'summary' => false,
            'columns' => [
                
                ['class' => 'yii\grid\ActionColumn',
                'template' => '{favorites}',
                 'buttons' =>
                     [
                         'favorites' => function ($url, $model) {
                                $certificates = new Certificates();
                                $certificate = $certificates->getCertificates();

                             $rows = (new \yii\db\Query())
                                ->from('favorites')
                                ->where(['certificate_id' => $certificate['id']])
                                ->andWhere(['program_id' => $model->program->id])
                                ->andWhere(['type' => 1])
                                ->one();
                             if ($rows) { 
                                  return Html::a('<span class="glyphicon glyphicon-star"></span>', Url::to(['/favorites/terminate2', 'id' => $model->program->id]), [
                                     'title' => Yii::t('yii', 'Убрать из избранного')
                                 ]);
                             }
                        },
                     ]
             ],
                [
                    'attribute'=>'program.name',
                    'label' => 'Наименование',
                ],
                [
                    'attribute'=>'year',
                    'value'=> function ($data) {
                         if ($data->program->year == 1) { return 'Однолетняя';}
                        if ($data->program->year == 2) { return 'Двулетняя';}
                        if ($data->program->year == 3) { return 'Трехлетняя';}
                        if ($data->program->year == 4) { return 'Четырехлетняя';}
                        if ($data->program->year == 5) { return 'Пятилетняя';}
                        if ($data->program->year == 6) { return 'Шестилетняя';}
                        if ($data->program->year == 7) { return 'Семилетняя';}
                    }
                    
                ],
                [
                     'attribute' => 'program.directivity',
                     'label' => 'Направленность',
                 ],
                
                [
                     'attribute' => 'program.zab',
                     'label' => 'Категория детей',
                    'value'=> function ($data) {
                         $zab = explode(',', $data->program->zab);
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
                     'attribute' => 'program.age_group_min',
                     'label' => 'Возраст от',
                 ],
                [
                     'attribute' => 'program.age_group_max',
                     'label' => 'Возраст до',
                 ],

                [
                     'attribute' => 'program.rating',
                     'label' => 'Рейтинг',
                 ],
                 [
                    'attribute'=>'program.mun',
                     'label' => 'Муниципалитет',
                    'filter'=>ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
                     'value' => function ($data) { 
                        $mun = (new \yii\db\Query())
                            ->select(['name'])
                            ->from('mun')
                            ->where(['id' => $data->program->mun])
                            ->one();
                         return $mun['name'];
                     },
                ],
                [
                     'label' => 'Цена*',
                     'value' => function ($data) { 
                        $year = (new \yii\db\Query())
                            ->select(['price'])
                            ->from('years')
                            ->where(['year' => 1])
                            ->andWhere(['program_id' => $data->program->id])
                            ->one();
                         return $year['price'];
                     },
                ],
                [
                     'label' => 'НС*',
                     'value' => function ($data) { 
                        $year = (new \yii\db\Query())
                            ->select(['normative_price'])
                            ->from('years')
                            ->where(['year' => 1])
                            ->andWhere(['program_id' => $data->program->id])
                            ->one();
                         return $year['normative_price'];
                     },
                ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'controller' => 'programs',
             'buttons' =>
                         [
                             'view' => function ($url, $model) {
                                 return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/programs/view', 'id' => $model->program->id]), [
                                     'title' => Yii::t('yii', 'Подтвердить создание договора')
                                 ]); },
                         ]
             ],
            
        ],
    ]); ?>
    </div>
</div>
