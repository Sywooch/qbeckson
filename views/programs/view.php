<?php

use kartik\tabs\TabsX;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use app\models\Organization;
use app\models\GroupsSearch;
use app\models\Payers;
use app\models\Certificates;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = $model->name;

if (Yii::$app->user->can('operators')) {
    $this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/operator-programs']];
} elseif (Yii::$app->user->can('organizations')) {
    $this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/organization-programs']];
}

$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .programs-view .affix {
        top: 20px;
        max-width: 33.3333333%;
    }
    @media only screen and (max-width: 992px) {
        .programs-view .affix-top, .programs-view .affix {
            top: 0;
            position: inherit;
            max-width: 100%;
            margin-bottom: 20px;
        }
    }
</style>

<div class="programs-view">
    <div class="clearfix">
        <?php
        if ($model->verification === 2) {
            if ($model->rating) {
                echo '<h1 class="pull-right">' . $model->rating . '%</h1>';
            } else {
                echo '<h4 class="pull-right">Рейтинга нет</h4>';
            }
        }
        ?>
        <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="affix-top col-md-12" data-spy="affix" data-offset-top="205">
                    <?php $items = []; ?>
                    <?php if (Yii::$app->user->can('organizations')) : ?>
                        <?php foreach ($model->modules as $key => $module) : ?>
                            <?php
                            $items[$key]['label'] = $module->getShortName();
                            $items[$key]['content'] = DetailView::widget([
                                'model' => $module,
                                'attributes' => [
                                    'fullname',
                                    [
                                        'attribute' => 'open',
                                        'label' => 'Зачисление',
                                        'format' => 'raw',
                                        //TODO переделать. На onf я пытался это разгребсти, но надо ещё сравнить логику и пересмотреть.
                                        'value' => function ($data) {
                                            $price = (new \yii\db\Query())
                                                ->select(['price'])
                                                ->from('years')
                                                ->where(['id' => $data->id])
                                                ->one();
                                            $organizations = new Organization();
                                            $organization = $organizations->getOrganization();
                                            if ($organization['actual'] !== 0) {
                                                if ($price['price'] > 0) {
                                                    if ($organization->type !== 4) {
                                                        if (!empty($organization['license_issued_dat']) && !empty($organization['fio']) && !empty($organization['position']) && !empty($organization['doc_type'])) {
                                                            if ($organization['doc_type'] === 1) {
                                                                if (!empty($organization['date_proxy']) && !empty($organization['number_proxy'])) {
                                                                    if ($data->open === 0) {
                                                                        return Html::a('Открыть', Url::to(['years/start', 'id' => $data->id]), ['class' => 'btn btn-success']);
                                                                    } else {
                                                                        return Html::a('Закрыть', Url::to(['years/stop', 'id' => $data->id]), ['class' => 'btn btn-danger']);
                                                                    }
                                                                } else {
                                                                    return 'Заполните информацию "Для договора"';
                                                                }
                                                            } else {
                                                                if ($data->open === 0) {
                                                                    return Html::a('Открыть', Url::to(['years/start', 'id' => $data->id]), ['class' => 'btn btn-success']);
                                                                } else {
                                                                    return Html::a('Закрыть', Url::to(['years/stop', 'id' => $data->id]), ['class' => 'btn btn-danger']);
                                                                }
                                                            }
                                                        } else {
                                                            return 'Заполните информацию "Для договора"';
                                                        }
                                                    } else {
                                                        if ($organization['doc_type'] === 1) {
                                                            if ($data->open === 0) {
                                                                return Html::a('Открыть', Url::to(['years/start', 'id' => $data->id]), ['class' => 'btn btn-success']);
                                                            } else {
                                                                return Html::a('Закрыть', Url::to(['years/stop', 'id' => $data->id]), ['class' => 'btn btn-danger']);
                                                            }
                                                        } else {
                                                            if ($data->open === 0) {
                                                                return Html::a('Открыть', Url::to(['years/start', 'id' => $data->id]), ['class' => 'btn btn-success']);
                                                            } else {
                                                                return Html::a('Закрыть', Url::to(['years/stop', 'id' => $data->id]), ['class' => 'btn btn-danger']);
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    return 'Нет цены';
                                                }
                                            } else {
                                                return 'Деятельность приостановлена';
                                            }
                                        }
                                    ],
                                    ['attribute' => 'price',
                                        'format' => 'raw',
                                        'value' => function ($data) {
                                            $price = (new \yii\db\Query())
                                                ->select(['price'])
                                                ->from('years')
                                                ->where(['id' => $data->id])
                                                ->one();

                                            if ($price['price'] > 0) {
                                                if ($data->open === 1) {
                                                    return $price['price'];
                                                } else {
                                                    return Html::a($price['price'], Url::to(['years/update', 'id' => $data->id]), ['class' => 'btn btn-primary']);
                                                }
                                            } else {
                                                return Html::a('Установить цену', Url::to(['years/update', 'id' => $data->id]), ['class' => 'btn btn-success']);
                                            }
                                        }
                                    ],
                                    'normative_price',
                                    [
                                        'label' => 'Число обучающихся',
                                        'value' => count($module->activeContracts),
                                    ],
                                    [
                                        'label' => 'Предварительные записи',
                                        'format' => 'raw',
                                        'value' => function ($data) {
                                            if ($data->previus === 1) {
                                                return Html::a('<span class="glyphicon glyphicon-ok green"></span>', Url::to(['years/prevstop', 'id' => $data->id]));
                                            }
                                            if ($data->previus === 0) {
                                                return Html::a('<span class="glyphicon glyphicon-remove red"></span>', Url::to(['years/prevstart', 'id' => $data->id]));
                                            }
                                        }
                                    ],
                                ],
                            ]);
                            ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <?php foreach ($model->modules as $key => $module) : ?>
                            <?php
                            $items[$key]['label'] = $module->getShortName();
                            $items[$key]['content'] = DetailView::widget([
                                'model' => $module,
                                'attributes' => [
                                    'fullname',
                                    [
                                        'value' => $module->open === 1 ? 'Да' : 'Нет',
                                        'label' => 'Зачисление',
                                    ],
                                    'price',
                                    'normative_price',
                                    [
                                        'label' => 'Число обучающихся',
                                        'value' => count($module->activeContracts),
                                    ],
                                    [
                                        'label' => 'Предварительные записи',
                                        'value' => $module->previus === 1 ? 'Да' : 'Нет',
                                    ],
                                ],
                            ]);
                            ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?= TabsX::widget([
                        'items' => $items,
                        'position' => TabsX::POS_LEFT,
                        'bordered' => true,
                        'encodeLabels' => false,
                    ]); ?>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <?php
            if (Yii::$app->user->can('organizations')) {
                if ($model->verification === 2 || $model->verification === 0) {
                    echo DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'directivity',
                            'commonActivities',
                            'limit',
                            [
                                'label' => 'Возраст детей',
                                'value' => 'с ' . $model->age_group_min . ' лет до ' . $model->age_group_max . ' лет',
                            ],
                            [
                                'attribute' => 'zab',
                                'label' => 'Категория детей',
                                'value' => $model->zabName($model->zab, $model->ovz),
                            ],
                            'task:ntext',
                            'annotation:ntext',
                            [
                                'attribute' => 'link',
                                'format' => 'raw',
                                'value' => Html::a(
                                    '<span class="glyphicon glyphicon-download-alt"></span>',
                                    '/' . $model->link
                                ),
                            ],
                            [
                                'attribute' => 'mun',
                                'value' => function ($model) {
                                    /** @var \app\models\Programs $model */
                                    return Html::a(
                                        $model->municipality->name,
                                        ['mun/view', 'id' => $model->municipality->id],
                                        ['target' => '_blank', 'data-pjax' => '0']
                                    );
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'ground',
                                'value' => $model->groundName($model->ground),
                            ],
                            [
                                'attribute' => 'norm_providing',
                                'label' => 'Нормы оснащения',
                            ],
                        ],
                    ]);
                }
                if ($model->verification === 1) {
                    echo DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'directivity',
                            [
                                'attribute' => 'activities',
                                'value' => function ($model) {
                                    /** @var \app\models\Programs $model */
                                    if ($model->activities) {
                                        return implode(', ', ArrayHelper::getColumn($model->activities, 'name'));
                                    }

                                    return $model->vid;
                                }
                            ],
                            [
                                'label' => 'Возраст детей',
                                'value' => 'с ' . $model->age_group_min . ' лет до ' . $model->age_group_max . ' лет',
                            ],
                            [
                                'attribute' => 'zab',
                                'label' => 'Категория детей',
                                'value' => $model->zabName($model->zab, $model->ovz),
                            ],
                            'task:ntext',
                            'annotation:ntext',
                            [
                                'attribute' => 'link',
                                'format' => 'raw',
                                'value' => Html::a('<span class="glyphicon glyphicon-download-alt"></span>', '/' . $model->link),
                            ],
                            [
                                'attribute' => 'mun',
                                'value' => function ($model) {
                                    /** @var \app\models\Programs $model */
                                    return Html::a(
                                        $model->municipality->name,
                                        ['mun/view', 'id' => $model->municipality->id],
                                        ['target' => '_blank', 'data-pjax' => '0']
                                    );
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'ground',
                                'value' => $model->groundName($model->ground),
                            ],
                            [
                                'attribute' => 'norm_providing',
                                'label' => 'Нормы оснащения',
                            ],
                        ],
                    ]);
                }
                if ($model->verification === 3) {
                    echo DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'label' => 'Причина отказа',
                                'value' => $model->otkazName($model->id),
                            ],
                            'directivity',
                            [
                                'attribute' => 'activities',
                                'value' => function ($model) {
                                    /** @var \app\models\Programs $model */
                                    if ($model->activities) {
                                        return implode(', ', ArrayHelper::getColumn($model->activities, 'name'));
                                    }

                                    return $model->vid;
                                }
                            ],
                            [
                                'label' => 'Возраст детей',
                                'value' => 'с ' . $model->age_group_min . ' лет до ' . $model->age_group_max . ' лет',
                            ],
                            [
                                'attribute' => 'zab',
                                'label' => 'Категория детей',
                                'value' => $model->zabName($model->zab, $model->ovz),
                            ],
                            'task:ntext',
                            'annotation:ntext',
                            [
                                'attribute' => 'link',
                                'format' => 'raw',
                                'value' => Html::a('<span class="glyphicon glyphicon-download-alt"></span>', '/' . $model->link),
                            ],
                            [
                                'attribute' => 'mun',
                                'value' => function ($model) {
                                    /** @var \app\models\Programs $model */
                                    return Html::a(
                                        $model->municipality->name,
                                        ['mun/view', 'id' => $model->municipality->id],
                                        ['target' => '_blank', 'data-pjax' => '0']
                                    );
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'ground',
                                'value' => $model->groundName($model->ground),
                            ],
                            [
                                'attribute' => 'norm_providing',
                                'label' => 'Нормы оснащения',
                            ],


                        ],
                    ]);
                }
            } else {
                if (Yii::$app->user->can('operators')) {
                    if ($model->verification === 3) {
                        echo DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                [
                                    'label' => 'Причина отказа',
                                    'value' => $model->otkazName($model->id),
                                ],
                                [
                                    'attribute' => 'organization.name',
                                    'format' => 'raw',
                                    'value' => Html::a($model->organization->name, Url::to(['/organization/view', 'id' => $model->organization->id]), ['class' => 'blue']),
                                ],
                                'directivity',
                                [
                                    'attribute' => 'activities',
                                    'value' => function ($model) {
                                        /** @var \app\models\Programs $model */
                                        if ($model->activities) {
                                            return implode(', ', ArrayHelper::getColumn($model->activities, 'name'));
                                        }

                                        return $model->vid;
                                    }
                                ],
                                'limit',
                                [
                                    'label' => 'Возраст детей',
                                    'value' => 'с ' . $model->age_group_min . ' лет до ' . $model->age_group_max . ' лет',
                                ],
                                [
                                    'attribute' => 'zab',
                                    'label' => 'Категория детей',
                                    'value' => $model->zabName($model->zab, $model->ovz),
                                ],
                                'task:ntext',
                                'annotation:ntext',
                                [
                                    'attribute' => 'link',
                                    'format' => 'raw',
                                    'value' => Html::a('<span class="glyphicon glyphicon-download-alt"></span>', '/' . $model->link),
                                ],
                                [
                                    'attribute' => 'mun',
                                    'value' => function ($model) {
                                        /** @var \app\models\Programs $model */
                                        return Html::a(
                                            $model->municipality->name,
                                            ['mun/view', 'id' => $model->municipality->id],
                                            ['target' => '_blank', 'data-pjax' => '0']
                                        );
                                    },
                                    'format' => 'raw',
                                ],
                                [
                                    'attribute' => 'ground',
                                    'value' => $model->groundName($model->ground),
                                ],
                                [
                                    'attribute' => 'norm_providing',
                                    'label' => 'Нормы оснащения',
                                ],
                            ],
                        ]);
                    } else {
                        echo DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                [
                                    'attribute' => 'organization.name',
                                    'format' => 'raw',
                                    'value' => Html::a($model->organization->name, Url::to(['/organization/view', 'id' => $model->organization->id]), ['class' => 'blue']),
                                ],
                                'directivity',
                                [
                                    'attribute' => 'activities',
                                    'value' => function ($model) {
                                        /** @var \app\models\Programs $model */
                                        if ($model->activities) {
                                            return implode(', ', ArrayHelper::getColumn($model->activities, 'name'));
                                        }

                                        return $model->vid;
                                    }
                                ],
                                'limit',
                                [
                                    'label' => 'Возраст детей',
                                    'value' => 'с ' . $model->age_group_min . ' лет до ' . $model->age_group_max . ' лет',
                                ],
                                [
                                    'attribute' => 'zab',
                                    'label' => 'Категория детей',
                                    'value' => $model->zabName($model->zab, $model->ovz),
                                ],
                                'task:ntext',
                                'annotation:ntext',

                                [
                                    'attribute' => 'link',
                                    'format' => 'raw',
                                    'value' => Html::a('<span class="glyphicon glyphicon-download-alt"></span>', '/' . $model->link),
                                ],
                                [
                                    'attribute' => 'mun',
                                    'value' => function ($model) {
                                        /** @var \app\models\Programs $model */
                                        return Html::a(
                                            $model->municipality->name,
                                            ['mun/view', 'id' => $model->municipality->id],
                                            ['target' => '_blank', 'data-pjax' => '0']
                                        );
                                    },
                                    'format' => 'raw',
                                ],
                                [
                                    'attribute' => 'ground',
                                    'value' => $model->groundName($model->ground),
                                ],
                                [
                                    'attribute' => 'norm_providing',
                                    'label' => 'Нормы оснащения',
                                ],
                            ],
                        ]);
                    }
                } else {
                    echo DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute' => 'organization.name',
                                'format' => 'raw',
                                'value' => Html::a($model->organization->name, Url::to(['/organization/view', 'id' => $model->organization->id]), ['class' => 'blue']),
                            ],
                            'directivity',
                            [
                                'attribute' => 'activities',
                                'value' => function ($model) {
                                    /** @var \app\models\Programs $model */
                                    if ($model->activities) {
                                        return implode(', ', ArrayHelper::getColumn($model->activities, 'name'));
                                    }

                                    return $model->vid;
                                }
                            ],
                            'limit',
                            [
                                'label' => 'Возраст детей',
                                'value' => 'с ' . $model->age_group_min . ' лет до ' . $model->age_group_max . ' лет',
                            ],
                            [
                                'attribute' => 'zab',
                                'label' => 'Категория детей',
                                'value' => $model->zabName($model->zab, $model->ovz),
                            ],
                            'task:ntext',
                            'annotation:ntext',
                            [
                                'attribute' => 'link',
                                'format' => 'raw',
                                'value' => Html::a(
                                    '<span class="glyphicon glyphicon-download-alt"></span>',
                                    '/' . $model->link
                                ),
                            ],
                            [
                                'attribute' => 'mun',
                                'value' => function ($model) {
                                    /** @var \app\models\Programs $model */
                                    return Html::a(
                                        $model->municipality->name,
                                        ['mun/view', 'id' => $model->municipality->id],
                                        ['target' => '_blank', 'data-pjax' => '0']
                                    );
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'ground',
                                'value' => $model->groundName($model->ground),
                            ],
                            [
                                'attribute' => 'norm_providing',
                                'label' => 'Нормы оснащения',
                            ],
                        ],
                    ]);
                }
            }
            ?>

            <?php
            $payers = new Payers();
            $payer = $payers->getPayer();

            if (Yii::$app->user->can('payer')) {
                $link = 'personal/payer-contracts';
            } elseif (Yii::$app->user->can('operators')) {
                $link = 'personal/operator-contracts';
            } elseif (Yii::$app->user->can('organizations')) {
                $link = 'personal/organization-contracts';
            }

            if ($model->verification === 2 && !Yii::$app->user->can('certificate')) {
                echo DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'label' => Html::a(
                                'Число обучающихся по программе',
                                [
                                    $link,
                                    'SearchActiveContracts[programName]' => $model->name,
                                    'SearchActiveContracts[payer_id]' => $model->id,
                                ],
                                ['class' => 'blue', 'target' => '_blank']
                            ),
                            'value' => $model->countContract($model->id, $payer),
                        ],
                    ],
                ]);
            } ?>

            <?php
            if (Yii::$app->user->can('payer')) {
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'ocen_fact',
                        'ocen_kadr',
                        'ocen_mat',
                        'ocen_obch',
                        'quality_control',
                    ],
                ]);
            }
            ?>

            <?php
            if (Yii::$app->user->can('operators')) {
                echo DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'ocen_fact',
                        'ocen_kadr',
                        'ocen_mat',
                        'ocen_obch',
                    ],
                ]);
            }
            ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'year',
                        'value' => $model->year,
                    ],
                    [
                        'label' => 'Общая продолжительность, часов',
                        'attribute' => 'countHours',
                    ],
                    [
                        'label' => 'Общая продолжительность, месяцев',
                        'attribute' => 'countMonths',
                    ],
                ],
            ]) ?>

            <?php
            if (!empty($years)) {

                $countyears = count($years);
                $i = 1;
                $form = ActiveForm::begin();
                foreach ($years as $value) {
                    echo '<div class="well">';
                    $label = $value->fullname;

                    if (Yii::$app->user->can('certificate')) {

                        $certificates = new Certificates();
                        $cert = $certificates->getCertificates();

                        if ($value->previus == 1 && $cert->actual == 1) {
                            $certificates = new Certificates();
                            $certificate = $certificates->getCertificates();

                            $rows = (new \yii\db\Query())
                                ->from('previus')
                                ->where(['certificate_id' => $certificate['id']])
                                ->andWhere(['year_id' => $value->id])
                                ->andWhere(['actual' => 1])
                                ->one();
                        }
                    }

                    if ($countyears > 1) {
                        echo '<a ng-click="collapsed' . $i . ' = !collapsed' . $i . '" ng-model="collapsed' . $i . '" href="javascript:void(0);">' . $value->fullname . '<span ng-class="{\'glyphicon glyphicon-menu-right pull-right\': !collapsed' . $i . ', \'glyphicon glyphicon-menu-down pull-right\': collapsed' . $i . '}"><span/></a>';
                    }

                    $group = [
                        'price',
                        'normative_price',
                        'month',
                        [
                            'label' => 'Часов по учебному плану',
                            'attribute' => 'hours',
                        ],
                        [
                            'label' => 'Наполняемость группы',
                            'value' => 'от ' . $value->minchild . ' до ' . $value->maxchild,
                        ],
                        [
                            'label' => 'Квалификация руководителя кружка',
                            'attribute' => 'kvfirst',
                        ],
                    ];

                    if ($value->hoursindivid) {
                        array_push($group,
                            [
                                'label' => 'Часов индивидуальных консультаций',
                                'attribute' => 'hoursindivid',
                            ]
                        );
                    }

                    if ($value->hoursdop) {
                        array_push($group,
                            [
                                'label' => 'Часов работы дополнительного педагога',
                                'attribute' => 'hoursdop',
                            ],
                            [
                                'label' => 'Квалификация дополнительного педагога',
                                'attribute' => 'kvdop',
                            ]
                        );
                    }

                    if (!empty($value->results)) {
                        array_push($group, ['attribute' => 'results']);
                    }

                    if ($countyears > 1) {
                        echo '<div ng-init="collapsed' . $i . ' = false" ng-show="collapsed' . $i . '"><br />';
                    }

                    echo DetailView::widget([
                        'model' => $value,
                        'attributes' => $group,
                    ]);

                    if ($model->verification == 2) {
                        $searchGroups = new GroupsSearch();
                        $searchGroups->year_id = $value->id;
                        $GroupsProvider = $searchGroups->search(Yii::$app->request->queryParams);

                        if (Yii::$app->user->can('certificate')) {
                            $count1 = (new \yii\db\Query())
                                ->select(['id'])
                                ->from('contracts')
                                ->where(['status' => [0, 1, 3]])
                                ->andWhere(['program_id' => $model->id])
                                ->count();

                            $count2 = (new \yii\db\Query())
                                ->select(['id'])
                                ->from('contracts')
                                ->where(['status' => [0, 1, 3, 5]])
                                ->andWhere(['organization_id' => $model->organization_id])
                                ->count();

                            $organization = Organization::findOne($model->organization_id);

                            //echo var_dump($organization->max_child);

                            $certificates = new Certificates();
                            $certificate = $certificates->getCertificates();

                            $programscolumn = (new \yii\db\Query())
                                ->select(['id'])
                                ->from('programs')
                                ->where(['direction_id' => $model->direction_id])
                                ->column();

                            $count3 = (new \yii\db\Query())
                                ->select(['id'])
                                ->from('contracts')
                                ->where(['status' => [0, 1, 3]])
                                ->andWhere(['payer_id' => $certificate->payer_id])
                                ->andWhere(['program_id' => $programscolumn])
                                ->count();

                            $payer = Payers::findOne($certificate->payer_id);

                            if ($model->directivity == 'Техническая (робототехника)') {
                                $limit_napr = $payer->directionality_1rob_count;
                            }
                            if ($model->directivity == 'Техническая (иная)') {
                                $limit_napr = $payer->directionality_1_count;
                            }
                            if ($model->directivity == 'Естественнонаучная') {
                                $limit_napr = $payer->directionality_2_count;
                            }
                            if ($model->directivity == 'Физкультурно-спортивная') {
                                $limit_napr = $payer->directionality_3_count;
                            }
                            if ($model->directivity == 'Художественная') {
                                $limit_napr = $payer->directionality_4_count;
                            }
                            if ($model->directivity == 'Туристско-краеведческая') {
                                $limit_napr = $payer->directionality_5_count;
                            }
                            if ($model->directivity == 'Социально-педагогическая') {
                                $limit_napr = $payer->directionality_6_count;
                            }

                            if ($cooperate != 0) {
                                if ($value->open == 0) {
                                    echo '<h4>Вы не можете записаться на программу. Зачисление закрыто.</h4>';
                                } else {
                                    if ($certificate->balance == 0) {
                                        echo '<h4>Вы не можете записаться на программу. Нет свободных средств на сертификате.</h4>';
                                    } else {
                                        if ($organization->actual == 0) {
                                            echo '<h4>Вы не можете записаться на программу. Действие организации приостановленно.</h4>';
                                        } else {
                                            if ($count3 >= $limit_napr) {
                                                echo '<h4>Вы не можете записаться на программу. Достигнут максимальный предел числа одновременно оплачиваемых вашей уполномоченной организацией услуг по данной направленности.</h4>';
                                            } else {
                                                if ($organization->max_child <= $count2) {
                                                    echo '<h4>Вы не можете записаться на программу. Достигнут максимальный лимит зачисления в организацию. Свяжитесь с представителем организации.</h4>';
                                                } else {
                                                    if ($model->limit <= $count1) {
                                                        echo '<h4>Достигнут максимальный лимит зачисления на обучение по программе. Свяжитесь с представителем организации.</h4>';
                                                    } else {


                                                        $certificates = new Certificates();
                                                        $certificate = $certificates->getCertificates();

                                                        $myprog = (new \yii\db\Query())
                                                            ->select(['program_id'])
                                                            ->from('contracts')
                                                            ->where(['certificate_id' => $certificate['id']])
                                                            ->andWhere(['status' => [0, 1, 3]])
                                                            ->column();

                                                        if (in_array($model['id'], $myprog)) {
                                                            echo '<p>Вы уже подали заявку на программу/заключили договор на обучение</p>';
                                                        } else {
                                                            echo '<p>Вы можете записаться на программу. Выберете группу:</p>';

                                                            echo GridView::widget([
                                                                'dataProvider' => $GroupsProvider,
                                                                'summary' => false,
                                                                'columns' => [

                                                                    'name',
                                                                    'address',
                                                                    'schedule',
                                                                    [
                                                                        'attribute' => 'datestart',
                                                                        'format' => 'date',
                                                                        'label' => 'Начало',
                                                                    ],
                                                                    [
                                                                        'attribute' => 'datestop',
                                                                        'format' => 'date',
                                                                        'label' => 'Конец',
                                                                    ],
                                                                    [
                                                                        'label' => 'Мест',
                                                                        'value' => function ($model) {

                                                                            $contract = (new \yii\db\Query())
                                                                                ->select(['id'])
                                                                                ->from('contracts')
                                                                                ->where(['status' => [0, 1, 3]])
                                                                                ->andWhere(['group_id' => $model->id])
                                                                                ->count();

                                                                            $years = (new \yii\db\Query())
                                                                                ->select(['maxchild'])
                                                                                ->from('years')
                                                                                ->where(['id' => $model->year_id])
                                                                                ->one();

                                                                            return $years['maxchild'] - $contract;
                                                                        }
                                                                    ],

                                                                    ['class' => 'yii\grid\ActionColumn',
                                                                        'template' => '{permit}',
                                                                        'buttons' =>
                                                                            [
                                                                                'permit' => function ($url, $model) {


                                                                                    $contract = (new \yii\db\Query())
                                                                                        ->select(['id'])
                                                                                        ->from('contracts')
                                                                                        ->where(['status' => [0, 1, 3]])
                                                                                        ->andWhere(['group_id' => $model->id])
                                                                                        ->count();

                                                                                    $years = (new \yii\db\Query())
                                                                                        ->select(['maxchild'])
                                                                                        ->from('years')
                                                                                        ->where(['id' => $model->year_id])
                                                                                        ->one();

                                                                                    $certificates = new Certificates();
                                                                                    $cert = $certificates->getCertificates();
                                                                                    $free = $years['maxchild'] - $contract;

                                                                                    //return var_dump($free);

                                                                                    if ($free != 0 && $cert->actual == 1) {
                                                                                        return Html::a('Выбрать', Url::to(['/contracts/new', 'id' => $model->id]), [
                                                                                            'class' => 'btn btn-success',
                                                                                            'title' => Yii::t('yii', 'Выбрать')
                                                                                        ]);
                                                                                    }

                                                                                },

                                                                            ]
                                                                    ],
                                                                ],
                                                            ]);
                                                        }

                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            echo GridView::widget([
                                'dataProvider' => $GroupsProvider,
                                'summary' => false,
                                'columns' => [

                                    'name',
                                    'address',
                                    'schedule',
                                    [
                                        'attribute' => 'datestart',
                                        'format' => 'date',
                                        'label' => 'Начало',
                                    ],
                                    [
                                        'attribute' => 'datestop',
                                        'format' => 'date',
                                        'label' => 'Конец',
                                    ],
                                    [
                                        'label' => 'Мест',
                                        'value' => function ($model) {

                                            $contract = (new \yii\db\Query())
                                                ->select(['id'])
                                                ->from('contracts')
                                                ->where(['status' => [0, 1, 3]])
                                                ->andWhere(['group_id' => $model->id])
                                                ->count();

                                            $years = (new \yii\db\Query())
                                                ->select(['maxchild'])
                                                ->from('years')
                                                ->where(['id' => $model->year_id])
                                                ->one();

                                            return $years['maxchild'] - $contract;
                                        }
                                    ],

                                    ['class' => 'yii\grid\ActionColumn',
                                        'template' => '{view}',
                                        'buttons' =>
                                            [
                                                'view' => function ($url, $model) {
                                                    if (Yii::$app->user->can('organizations')) {
                                                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/groups/contracts', 'id' => $model->id]));
                                                    }
                                                },
                                            ]
                                    ],
                                ],
                            ]);

                            if (Yii::$app->user->can('organizations')) {
                                echo Html::a('Добавить группу', Url::to(['/groups/newgroup', 'id' => $value->id]), ['class' => 'btn btn-primary']);
                            }


                        }

                    }
                    if ($countyears > 1) {
                        echo '</div>';
                    }

                    $i++;
                    echo '</div>';
                }
                ActiveForm::end();
            }

            ?>
        </div>
    </div>
    <?php
    if (Yii::$app->user->can('operators')) {

        echo Html::a('Пересчитать нормативную стоимость', Url::to(['/programs/newnormprice', 'id' => $model->id]), ['class' => 'btn btn-primary']);
        echo '&nbsp;';
        echo Html::a('Пересчитать лимит', Url::to(['/programs/newlimit', 'id' => $model->id]), ['class' => 'btn btn-primary']);
        echo '&nbsp;';
        echo Html::a('Пересчитать рейтинг', Url::to(['/programs/raiting', 'id' => $model->id]), ['class' => 'btn btn-primary']);
        echo '<br><br>';
        echo Html::a('Назад', '/personal/operator-programs', ['class' => 'btn btn-primary']);
    }
    if (Yii::$app->user->can('certificate')) {
        echo Html::a('Назад', '/programs/search', ['class' => 'btn btn-primary']);
    }
    if (Yii::$app->user->can('payer')) {
        echo Html::a('Назад', '/personal/payer-programs', ['class' => 'btn btn-primary']);
    }
    if (Yii::$app->user->can('organizations')) {
        echo Html::a('Назад', '/personal/organization-programs', ['class' => 'btn btn-primary']);

        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        if ($organization['actual'] != 0) {
            echo "&nbsp;";
            $contracts = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['program_id' => $model->id, 'status' => 1])
                ->count();

            $open = (new \yii\db\Query())
                ->select(['id'])
                ->from('years')
                ->where(['program_id' => $model->id])
                ->andWhere(['open' => 1])
                ->count();

            // return var_dump($open);

            if ($open == 0 and $contracts == 0) {
                echo Html::a('Редактировать', Url::to(['/programs/update', 'id' => $model->id]), ['class' => 'btn btn-primary']);
                echo "&nbsp;";
                echo "<div class='pull-right'>";
                echo Html::a('Удалить', Url::to(['/programs/delete', 'id' => $model->id]), ['class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить программу?',
                        'method' => 'post']]);
                echo "</div>";
            }
        }
    }
    ?>
</div>
