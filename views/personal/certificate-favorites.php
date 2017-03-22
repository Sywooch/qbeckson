<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Mun;
use app\models\Certificates;

/* @var $this yii\web\View */
?>

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <?= GridView::widget([
            'dataProvider' => $FavoritesProvider,
            'filterModel' => $searchFavorites,
            'pjax' => true,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                if ($model) {

                    $certificates = new Certificates();
                    $certificate = $certificates->getCertificates();

                    $rows = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('cooperate')
                        ->where(['payer_id' => $certificate['payer_id']])
                        ->andWhere(['organization_id' => $model['organization_id']])
                        ->andWhere(['status' => 1])
                        ->count();

                    if ($rows == 0) {
                        return ['class' => 'danger'];
                    }

                    $org = (new \yii\db\Query())
                        ->select(['actual'])
                        ->from('organization')
                        ->where(['id' => $model['organization_id']])
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
                    'attribute' => 'program.name',
                    'label' => 'Наименование',
                ],
                [
                    'attribute' => 'program.year',
                    'value' => function ($data) {
                        return Yii::$app->i18n->messageFormatter->format(
                            '{n, plural, one{# модуль} few{# модуля} many{# модулей} other{# модуля}}',
                            ['n' => $data->program->year],
                            Yii::$app->language
                        );
                    }
                ],
                [
                    'attribute' => 'program.directivity',
                    'label' => 'Направленность',
                ],

                [
                    'attribute' => 'program.zab',
                    'label' => 'Категория детей',
                    'value' => function ($data) {
                        $zab = explode(',', $data->program->zab);
                        $display = '';
                        foreach ($zab as $value) {
                            if ($value == 1) {
                                $display = $display . ', глухие';
                            }
                            if ($value == 2) {
                                $display = $display . ', слабослышащие и позднооглохшие';
                            }
                            if ($value == 3) {
                                $display = $display . ', слепые';
                            }
                            if ($value == 4) {
                                $display = $display . ', слабовидящие';
                            }
                            if ($value == 5) {
                                $display = $display . ', нарушения речи';
                            }
                            if ($value == 6) {
                                $display = $display . ', фонетико-фонематическое нарушение речи';
                            }
                            if ($value == 7) {
                                $display = $display . ', нарушение опорно-двигательного аппарата';
                            }
                            if ($value == 8) {
                                $display = $display . ', задержка психического развития';
                            }
                            if ($value == 9) {
                                $display = $display . ', расстройство аутистического спектра';
                            }
                            if ($value == 10) {
                                $display = $display . ', нарушение интеллекта';
                            }
                        }
                        if ($display == '') {
                            return 'без ОВЗ';
                        } else {
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
                    'attribute' => 'program.mun',
                    'label' => 'Муниципалитет',
                    'filter' => ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
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
                                ]);
                            },
                        ]
                ],

            ],
        ]); ?>
    </div>
</div>
