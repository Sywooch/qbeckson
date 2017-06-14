<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\Informs;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use app\models\ProgrammeModuleSearch;
use yii\helpers\ArrayHelper;
use app\models\Mun;


/* @var $this yii\web\View */

$this->title = 'Программы';
$this->params['breadcrumbs'][] = $this->title;
?>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Сертифицированные <span
                    class="badge"><?= $Programs1Provider->getTotalCount() ?></span></a></li>
    <li><a data-toggle="tab" href="#panel2">Ожидающие сертификации <span
                    class="badge"><?= $waitProgramsProvider->getTotalCount() ?></span></a></li>
    <li><a data-toggle="tab" href="#panel3">Отказано в сертификации <span
                    class="badge"><?= $Programs2Provider->getTotalCount() ?></span></a></li>
</ul>
<br>

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <p class="text-right">

            <?= Html::a('Пересчитать нормативные стоимости', ['years/allnormprice'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Пересчитать лимиты', ['programs/alllimit'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Пересчитать рейтинги', ['programs/allraiting'], ['class' => 'btn btn-success']) ?>

        </p>


        <?= GridView::widget([
            'dataProvider' => $Programs1Provider,
            'filterModel' => $searchPrograms1,
            'resizableColumns' => true,
            'pjax' => true,
            'summary' => false,
            'columns' => [
                [
                    'attribute' => 'name',
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
                    'value' => function ($data) {
                        $zab = explode(',', $data->zab);
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
                    'value' => function ($data) {

                        $organization = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('organization')
                            ->where(['name' => $data->organization->name])
                            ->one();


                        return Html::a($data->organization->name, Url::to(['/organization/view', 'id' => $organization['id']]), ['class' => 'blue', 'target' => '_blank']);
                    },
                ],
                [
                    'attribute' => 'mun', 'label' => 'Муниципалитет',
                    'filter' => ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
                    'value' => function ($data) {
                        $mun = (new \yii\db\Query())
                            ->select(['name'])
                            ->from('mun')
                            ->where(['id' => $data->mun])
                            ->one();

                        return $mun['name'];
                    },
                ],

                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'programs',
                    'template' => '{view}',
                ],
            ],
        ]); ?>

        <?= ExportMenu::widget([
            'dataProvider' => $Programs1Provider,
            'target' => '_self',
            'columns' => [
                [
                    'attribute' => 'name',
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
                    'value' => function ($data) {
                        $zab = explode(',', $data->zab);
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
                    'value' => function ($data) {

                        $organization = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('organization')
                            ->where(['name' => $data->organization->name])
                            ->one();


                        return Html::a($data->organization->name, Url::to(['/organization/view', 'id' => $organization['id']]), ['class' => 'blue', 'target' => '_blank']);
                    },
                ],
                [
                    'attribute' => 'mun',
                    'label' => 'Муниципалитет',
                    'filter' => ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
                    'value' => function ($data) {
                        $mun = (new \yii\db\Query())
                            ->select(['name'])
                            ->from('mun')
                            ->where(['id' => $data->mun])
                            ->one();

                        return $mun['name'];
                    },
                ],
            ],
        ]); ?>
    </div>

    <div id="panel2" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $waitProgramsProvider,
            'filterModel' => $searchWaitPrograms,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                if ($model->verification == 1) {
                    return ['class' => 'danger'];
                }
            },
            'pjax' => true,
            'columns' => [
                'name',
                [
                    'attribute' => 'organization',
                    'label' => 'Наименование организации',
                    'format' => 'raw',
                    'value' => function ($data) {

                        $organization = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('organization')
                            ->where(['name' => $data->organization->name])
                            ->one();


                        return Html::a($data->organization->name, Url::to(['/organization/view', 'id' => $organization['id']]), ['class' => 'blue', 'target' => '_blank']);
                    },
                ],
                'directivity',
                'commonActivities',

                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'programs',
                    'template' => '{permit}',
                    'buttons' =>
                        [
                            'permit' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-check"></span>', Url::to(['/programs/verificate', 'id' => $model->id]), [
                                    'title' => Yii::t('yii', 'Сертифицировать программу')
                                ]);
                            },

                            'decertificate' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::to(['/programs/decertificate', 'id' => $model->id]), [
                                    'title' => Yii::t('yii', 'Отказать в сертификации программы')
                                ]);
                            },

                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/programs/edit', 'id' => $model->id]), [
                                    'title' => Yii::t('yii', 'Редактировать программу')
                                ]);
                            },
                        ]
                ],
            ],
        ]); ?>

        <?= ExportMenu::widget([
            'dataProvider' => $waitProgramsProvider,
            'target' => '_self',
            'columns' => [
                'name',
                [
                    'attribute' => 'organization',
                    'value' => 'organization.name',
                    'label' => 'Наименование организации',
                ],
                'directivity',
                'vid',
            ],
        ]); ?>
    </div>
    <div id="panel3" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $Programs2Provider,
            'filterModel' => $searchPrograms2,
            'pjax' => true,
            'columns' => [

                [
                    'attribute' => 'name',
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
                    'attribute' => 'organization',
                    'label' => 'Организация',
                    'format' => 'raw',
                    'value' => function ($data) {

                        $organization = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('organization')
                            ->where(['name' => $data->organization->name])
                            ->one();


                        return Html::a($data->organization->name, Url::to(['/organization/view', 'id' => $organization['id']]), ['class' => 'blue', 'target' => '_blank']);
                    },
                ],
                [
                    'attribute' => 'zab',
                    'label' => 'Категория детей',
                    'value' => function ($data) {
                        $zab = explode(',', $data->zab);
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
    <br>
    <?php
    echo ExportMenu::widget([
        'dataProvider' => $ProgramsallProvider,
        'target' => '_self',
        //'showConfirmAlert' => false,
        //'enableFormatter' => false,
        'showColumnSelector' => false,
        //'contentBefore' => [
        //    'value' => 123,
        //],
        'filename' => 'programs',
        'dropdownOptions' => [
            'class' => 'btn btn-success',
            'label' => 'Программы',
            'icon' => false,
        ],
        //'asDropdown' => false,
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_PDF => false,
            ExportMenu::FORMAT_CSV => false,
            ExportMenu::FORMAT_HTML => false,
            ExportMenu::FORMAT_EXCEL => false,
        ],
        'columns' => [
            'id',
            'organization_id',
            'verification',
            //'verification' => 'Запись о подтверждении прохождения экспертизы и доступности программы для выбора',
            'form',
            'name',
            'directivity',
            'vid',
            'mun',
            'annotation',
            'task',
            'age_group_min',
            'age_group_max',
            'ovz',
            'zab',
            'year',
            'norm_providing',
            'ground',
            'rating',
            'limit',
            'link',
            'edit',
            'p3z',
            //'price_next' => 'Ожидаемая стоимость будущего года',
            //'certification_date' => 'Дата направления программы на сертификацию',
            //'colse_date' => 'Дата завершения реализации программы',
            'study',
            'last_contracts',
            'last_s_contracts',
            'last_s_contracts_rod',
            'quality_control',
            //'both_teachers' => 'Число педагогических работников, одновременно реализующих программу',
            //'fullness' => 'Наполняемость группы при реализации программы',
            //'complexity' => 'Сложность оборудования и средств обучения используемых при реализации программы',
            'ocen_fact',
            'ocen_kadr',
            'ocen_mat',
            'ocen_obch',
        ],

    ]);

    echo '&nbsp;';

    echo ExportMenu::widget([
        'dataProvider' => $YearsallProvider,
        'target' => '_self',
        //'showConfirmAlert' => false,
        //'enableFormatter' => false,
        'showColumnSelector' => false,
        //'contentBefore' => [
        //    'value' => 123,
        //],
        'filename' => 'years',
        'dropdownOptions' => [
            'class' => 'btn btn-success',
            'label' => 'Модули',
            'icon' => false,
        ],
        //'asDropdown' => false,
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_PDF => false,
            ExportMenu::FORMAT_CSV => false,
            ExportMenu::FORMAT_HTML => false,
            ExportMenu::FORMAT_EXCEL => false,
        ],
        'columns' => [
            'id',
            'program_id',
            'year',
            'month',

            'hours',
            'kvfirst',
            'hoursindivid',
            'hoursdop',
            'kvdop',
            'minchild',
            'maxchild',
            'price',
            'normative_price',

            //'rating' => 'Рейтинг',
            //'limits' => 'Лимит зачисления',
            'open',
            'previus',
            'quality_control',

            'p21z',
            'p22z',
        ],

    ]);

    echo '&nbsp;';

    echo ExportMenu::widget([
        'dataProvider' => $GroupsallProvider,
        'target' => '_self',
        //'showConfirmAlert' => false,
        //'enableFormatter' => false,
        'showColumnSelector' => false,
        //'contentBefore' => [
        //    'value' => 123,
        //],
        'filename' => 'years',
        'dropdownOptions' => [
            'class' => 'btn btn-success',
            'label' => 'Группы',
            'icon' => false,
        ],
        //'asDropdown' => false,
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_PDF => false,
            ExportMenu::FORMAT_CSV => false,
            ExportMenu::FORMAT_HTML => false,
            ExportMenu::FORMAT_EXCEL => false,
        ],
        'columns' => [
            'id',
            'organization_id',
            'program_id',
            'year_id',
            'name',
            'address',
            'schedule',
            'datestart',
            'datestop',
        ],

    ]);
    ?>
</div>
