<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\YearsCertSearch;
use app\models\Organization;

$this->title = 'Программы';
   $this->params['breadcrumbs'][] = 'Программы';
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

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Сертифицированные <span class="badge"><?= $Programs1Provider->getTotalCount() ?></span></a></li>
    <li><a data-toggle="tab" href="#panel2">Ожидающие сертификации <span class="badge"><?= $Programs0Provider->getTotalCount() ?></span></a></li>
    <li><a data-toggle="tab" href="#panel3">Отказано в сертификации <span class="badge"><?= $Programs2Provider->getTotalCount() ?></span></a></li>
</ul>
<br>


<div class="tab-content">
  
   <?php
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
    $organizations = new Organization();
    $organization = $organizations->getOrganization();
    if ($roles['organizations'] and $organization['actual'] != 0) {
        
        echo "<p>";
        echo Html::a('Отправить программу на сертификацию', ['programs/create'], ['class' => 'btn btn-success']); 
        echo "</p>";
    }
    ?>
    <div id="panel1" class="tab-pane fade in active">
        <?= GridView::widget([
            'dataProvider' => $Programs1Provider,
            'filterModel' => $searchPrograms1,
            'summary' => false,
            'pjax'=>true,
            'columns' => [
                [
                    'class'=>'kartik\grid\ExpandRowColumn',
                    'width'=>'50px',
                    'value'=>function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detail'=>function ($model, $key, $index, $column) {
                        $searchModel = new YearsCertSearch();
                        $searchModel->program_id = $model->id;
                        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                        return Yii::$app->controller->renderPartial('/years/main', ['searchModel'=>$searchModel, 'dataProvider'=>$dataProvider]);
                    },
                    'headerOptions'=>['class'=>'kartik-sheet-style'], 
                    'expandOneOnly'=>true
                ],
                [
                    'attribute'=>'name',
                    'label' => 'Наименование',
                ],
                [
                    'attribute'=>'year',
                    'value'=> function ($data) {
                         if ($data->year == 1) { return 'Однолетняя';}
                        if ($data->year == 2) { return 'Двухлетняя';}
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
                 
                /*[
                    'attribute'=>'link',
                    'format' => 'raw',
                    'value' => function($data){
                        return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', '/'.$data->link );
                    }
                ],
                [
                  'label' => 'Предварительные записи',
                  'format' => 'raw',
                  'value' => function($data){
                      $previus = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('previus')
                        ->where(['program_id' => $data->id])
                        ->count();
                         return Html::a($previus, Url::to(['/personal/organization-favorites', 'program' => $data->id]));
                  }
                 ],*/

                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'programs',
                    'template' => '{view}',
                 ],
            ],
        ]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $Programs0Provider,
            'filterModel' => $searchPrograms0,
            'pjax'=>true,
            'rowOptions' => function ($model, $index, $widget, $grid){
              if($model->verification == 1){
                return ['class' => 'info'];
              }
            },
            'columns' => [

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

    

                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'programs',
                    'template' => '{view}',
                 ],
            ],
        ]); ?>
    </div>
    <div id="panel3" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $Programs2Provider,
            'filterModel' => $searchPrograms2,
            'pjax'=>true,
            'columns' => [
                
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

                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'programs',
                    'template' => '{view}',
                 ],
            ],
        ]); ?>
    </div>
</div>
