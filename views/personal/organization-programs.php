<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\ProgrammeModuleCertSearch;

/* @var $this yii\web\View */

$this->title = 'Программы';
$this->params['breadcrumbs'][] = $this->title;
?>
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Сертифицированные <span class="badge"><?= $Programs1Provider->getTotalCount() ?></span></a></li>
    <li><a data-toggle="tab" href="#panel2">Ожидающие сертификации <span class="badge"><?= $waitProgramsProvider->getTotalCount() ?></span></a></li>
    <li><a data-toggle="tab" href="#panel3">Отказано в сертификации <span class="badge"><?= $Programs2Provider->getTotalCount() ?></span></a></li>
</ul>
<br>


<div class="tab-content">
    <?php
        if (Yii::$app->user->can('organizations') && Yii::$app->user->identity->organization->actual > 0) {
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
                        $searchModel = new ProgrammeModuleCertSearch();
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

                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'programs',
                    'template' => '{view}',
                ],
            ],
        ]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $waitProgramsProvider,
            'filterModel' => $searchWaitPrograms,
            'pjax' => true,
            'rowOptions' => function ($model, $index, $widget, $grid){
              if($model->verification == 1){
                return ['class' => 'info'];
              }
            },
            'columns' => [
                [
                    'attribute' => 'organization',
                    'value' => 'organization.name'
                ],
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
