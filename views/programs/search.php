<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use app\models\Certificates;
use app\models\ProgrammeModuleSearch;
use yii\helpers\ArrayHelper;
use app\models\Mun;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProgramsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Поиск программ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-index">
    <?php if (Yii::$app->user->can('certificate')) : ?>
        <div class="row">
            <div class="col-md-12">
                <div class="pull-right">
                    <?= $this->render('../common/_select-municipality-modal') ?>
                </div>
            </div>
        </div>
        <br>
    <?php endif; ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
                                ->andWhere(['program_id' => $model->id])
                                ->andWhere(['type' => 1])
                                ->one();
                             if (!$rows) {
                                  return Html::a('<span class="glyphicon glyphicon-star-empty"></span>', Url::to(['/favorites/new', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Добавить в избранное')
                                 ]);
                             } else {
                                  return Html::a('<span class="glyphicon glyphicon-star"></span>', Url::to(['/favorites/terminate', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Убрать из избранного')
                                 ]);
                             }
                        },
                     ]
             ],
                [
                    'attribute'=>'name',
                    'label' => 'Наименование',
                ],
                [
                    'attribute' => 'year',
                    'value' => function ($data) {
                        return Yii::$app->i18n->messageFormatter->format(
                            '{n, plural, one{# модуль} few{# модуля} many{# модулей} other{# модуля}}',
                            ['n' => $data->year],
                            Yii::$app->language
                        );
                    }
                ],
                'countHours',
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
                [
                     'label' => 'Цена*',
                     'value' => function ($data) { 
                        $year = (new \yii\db\Query())
                            ->select(['price'])
                            ->from('years')
                            ->where(['year' => 1])
                            ->andWhere(['program_id' => $data->id])
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
                            ->andWhere(['program_id' => $data->id])
                            ->one();
                         return $year['normative_price'];
                     },
                ],
                [
                     'label' => 'Соглашение',
                     'value' => function ($data) { 
                          $certificates = new Certificates();
                            $certificate = $certificates->getCertificates();
                         
                      $rows = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('cooperate')
                        ->where(['payer_id'=> $certificate['payer_id']])
                        ->andWhere(['organization_id' => $data['organization_id']])
                        ->andWhere(['status'=> 1])
                        ->count();

                          if ($rows == 0) {
                            return 'Нет';
                            }
                         else {
                            return 'Да';
                            }
                     },
                ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
             ],
            
        ],
    ]); ?>
</div>
<br>
<br>
<p class="minitext">* Цена программы и нормативная стоимость (НС) многолетних программ указаны за первый год обучения.</p>
