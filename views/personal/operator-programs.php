<?php

use app\helpers\GridviewHelper;
use app\models\Mun;
use app\models\statics\DirectoryProgramDirection;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchOpenPrograms \app\models\search\ProgramsSearch */
/* @var $searchWaitPrograms \app\models\search\ProgramsSearch */
/* @var $searchNewWaitPrograms \app\models\search\ProgramsSearch */
/* @var $searchAfterRefuseWaitPrograms \app\models\search\ProgramsSearch */
/* @var $searchClosedPrograms \app\models\search\ProgramsSearch */
/* @var $openProgramsProvider \yii\data\ActiveDataProvider */
/* @var $waitProgramsProvider \yii\data\ActiveDataProvider */
/* @var $afterRefuseWaitProgramsProvider \yii\data\ActiveDataProvider */
/* @var $newWaitProgramsProvider \yii\data\ActiveDataProvider */
/* @var $closedProgramsProvider \yii\data\ActiveDataProvider */
/* @var $allOpenProgramsProvider \yii\data\ActiveDataProvider */
/* @var $allWaitProgramsProvider \yii\data\ActiveDataProvider */
/* @var $allAfterRefuseWaitProgramsProvider \yii\data\ActiveDataProvider */
/* @var $programsAllProvider \yii\data\ActiveDataProvider */
/* @var $yearsAllProvider \yii\data\ActiveDataProvider */
/* @var $groupsAllProvider \yii\data\ActiveDataProvider */

$this->title = 'Программы';
$this->params['breadcrumbs'][] = $this->title;

$zab = [
    'type' => SearchFilter::TYPE_SELECT2,
    'data' => $searchOpenPrograms::illnesses(),
    'attribute' => 'zab',
    'label' => 'Категория детей',
    'value' => function ($model) {
        return $model->illnessesList;
    }
];
$year = [
    'attribute' => 'year',
    'value' => function ($model) {
        /** @var \app\models\Programs $model */
        return Yii::$app->i18n->messageFormatter->format(
            '{n, plural, one{# модуль} few{# модуля} many{# модулей} other{# модуля}}',
            ['n' => count($model->years)],
            Yii::$app->language
        );
    },
    'type' => SearchFilter::TYPE_TOUCH_SPIN,
];
$organization = [
    'attribute' => 'organization',
    'label' => 'Организация',
    'format' => 'raw',
    'value' => function ($model) {
        /** @var \app\models\Programs $model */
        return Html::a(
            $model->organization->name,
            Url::to(['organization/view', 'id' => $model->organization_id]),
            ['target' => '_blank', 'data-pjax' => '0']
        );
    },
];
$municipality = [
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
    'label' => 'Муниципалитет',
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
];
$name = [
    'label' => 'Наименование',
    'attribute' => 'name'
];
$hours = [
    'attribute' => 'hours',
    'value' => 'countHours',
    'label' => 'Кол-во часов',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 2000
    ]
];
$directivity = [
    'attribute' => 'direction_id',
    'value' => 'direction.name',
    'label' => 'Направленность',
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => ArrayHelper::map(DirectoryProgramDirection::find()->all(), 'id', 'name'),
];
$form = [
    'attribute' => 'form',
    'value' => function ($model) {
        return \app\models\Programs::forms()[$model->form];
    },
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => $searchOpenPrograms::forms(),
];
$ageGroupMin = [
    'attribute' => 'age_group_min',
    'label' => 'Возраст от',
    'type' => SearchFilter::TYPE_TOUCH_SPIN,
];
$ageGroupMax = [
    'attribute' => 'age_group_max',
    'label' => 'Возраст до',
    'type' => SearchFilter::TYPE_TOUCH_SPIN,
];
$rating = [
    'attribute' => 'rating',
    'label' => 'Рейтинг',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 100
    ]
];
$limit = [
    'attribute' => 'limit',
    'label' => 'Лимит',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 10000
    ]
];
$actions = [
    'class' => ActionColumn::class,
    'controller' => 'programs',
    'template' => '{view}',
    'searchFilter' => false,
];

$count = [
    'attribute' => 'currentActiveContractsCount',
    'searchFilter' => false,
    'export' => true,
];

$openColumns = [
    $name,
    $year,
    $hours,
    $directivity,
    $form,
    $zab,
    $ageGroupMin,
    $ageGroupMax,
    $rating,
    $limit,
    $organization,
    $count,
    $municipality,
    [
        'attribute' => 'organization_id',
        'type' => SearchFilter::TYPE_HIDDEN,
    ],
    $actions,
];
$waitColumns = [
    $name,
    $year,
    $hours,
    $directivity,
    $form,
    $zab,
    $ageGroupMin,
    $ageGroupMax,
    $organization,
    $municipality,
    [
        'class' => ActionColumn::class,
        'controller' => 'programs',
        'template' => '{permit}',
        'buttons' => [
            'permit' => function ($url, $model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-check"></span>',
                    Url::to(['/programs/verificate', 'id' => $model->id]),
                    ['title' => 'Сертифицировать программу']
                );
            },
            'decertificate' => function ($url, $model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-remove"></span>',
                    Url::to(['/programs/decertificate', 'id' => $model->id]),
                    ['title' => 'Отказать в сертификации программы']
                );
            },
            'update' => function ($url, $model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-pencil"></span>',
                    Url::to(['/programs/edit', 'id' => $model->id]),
                    ['title' => 'Редактировать программу']
                );
            },
        ],
        'searchFilter' => false,
    ],
];
$closedPrograms = [
    $municipality,
    $name,
    $year,
    $hours,
    $directivity,
    $form,
    $zab,
    $ageGroupMin,
    $ageGroupMax,
    $organization,
    $actions
];

$preparedOpenColumns = GridviewHelper::prepareColumns('programs', $openColumns, 'open');
$preparedWaitColumns = GridviewHelper::prepareColumns('programs', $waitColumns, 'wait');
$preparedClosedPrograms = GridviewHelper::prepareColumns('programs', $closedPrograms, 'close');
?>
<ul class="nav nav-tabs">
    <li class="active">
        <a data-toggle="tab" href="#panel1">Сертифицированные
            <span class="badge"><?= $openProgramsProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#wait-panel">Ожидающие сертификации
            <span class="badge"><?= $waitProgramsProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel3">Отказано в сертификации
            <span class="badge"><?= $closedProgramsProvider->getTotalCount() ?></span>
        </a>
    </li>
</ul>
<br>
<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <?php if ($searchOpenPrograms->organization_id) : ?>
            <p class="lead">Показаны результаты для организации: <?= $searchOpenPrograms->organization; ?></p>
        <?php endif; ?>
        <?= SearchFilter::widget([
            'model' => $searchOpenPrograms,
            'action' => ['personal/operator-programs#panel1'],
            'data' => GridviewHelper::prepareColumns(
                'programs',
                $openColumns,
                'open',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'open'
        ]); ?>

        <?= Html::a('Пересчитать нормативные стоимости', ['years/allnormprice'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Пересчитать лимиты', ['programs/alllimit'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Пересчитать рейтинги', ['programs/allraiting'], ['class' => 'btn btn-success']) ?>
        <br>
        <br>
        <?= GridView::widget([
            'dataProvider' => $openProgramsProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedOpenColumns,
        ]); ?>
        <?= \app\widgets\Export::widget([
            'dataProvider' => $allOpenProgramsProvider,
            'columns' => GridviewHelper::prepareColumns('programs', $openColumns, 'open', 'export'),
            'group' => 'operator-open-programs',
            'table' => 'programs',
            'redirectUrl' => 'operator-programs'
        ]); ?>
    </div>
    <div id="wait-panel" class="tab-pane fade">
        <ul class="nav nav-tabs">
            <li class="active">
                <a data-toggle="tab" href="#wait-all">Все
                    <span class="badge"><?= $waitProgramsProvider->getTotalCount() ?></span>
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#wait-new">Новые
                    <span class="badge"><?= $newWaitProgramsProvider->getTotalCount() ?></span>
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#wait-after-refuse">Повторная попытка
                    <span class="badge"><?= $afterRefuseWaitProgramsProvider->getTotalCount() ?></span>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="wait-all" class="tab-pane fade in active">
                <?= SearchFilter::widget([
                    'model' => $searchWaitPrograms,
                    'action' => ['personal/operator-programs#wait-all'],
                    'data' => GridviewHelper::prepareColumns(
                        'programs',
                        $waitColumns,
                        'wait',
                        'searchFilter',
                        null
                    ),
                    'role' => UserIdentity::ROLE_OPERATOR,
                    'type' => 'wait'
                ]); ?>
                <?= GridView::widget([
                    'dataProvider' => $waitProgramsProvider,
                    'filterModel' => null,
                    'rowOptions' => function ($model, $index, $widget, $grid) {
                        /** @var \app\models\Programs $model */
                        if ($model->verification === \app\models\Programs::VERIFICATION_WAIT) {
                            return ['class' => 'danger'];
                        } elseif ($model->verification !== \app\models\Programs::VERIFICATION_WAIT
                            && $model->verification !== \app\models\Programs::VERIFICATION_UNDEFINED
                        ) {
                            return [
                                'class' => 'warning',
                            ];
                        }
                    },
                    'summary' => false,
                    'pjax' => true,
                    'columns' => $preparedWaitColumns,
                ]); ?>
                <?= \app\widgets\Export::widget([
                    'dataProvider' => $allWaitProgramsProvider,
                    'columns' => GridviewHelper::prepareColumns('programs', $waitColumns, 'wait', 'export'),
                    'group' => 'operator-wait-programs',
                    'table' => 'programs',
                    'redirectUrl' => 'operator-programs'
                ]); ?>
            </div>
            <div id="wait-new" class="tab-pane fade">
                <?= SearchFilter::widget([
                    'model' => $searchNewWaitPrograms,
                    'action' => ['personal/operator-programs#wait-new'],
                    'data' => GridviewHelper::prepareColumns(
                        'programs',
                        $waitColumns,
                        'wait',
                        'searchFilter',
                        null
                    ),
                    'role' => UserIdentity::ROLE_OPERATOR,
                    'type' => 'wait'
                ]); ?>
                <?= GridView::widget([
                    'dataProvider' => $newWaitProgramsProvider,
                    'filterModel' => null,
                    'rowOptions' => function ($model, $index, $widget, $grid) {
                        /** @var \app\models\Programs $model */
                        if ($model->verification === \app\models\Programs::VERIFICATION_WAIT) {
                            return ['class' => 'danger'];
                        } elseif ($model->verification !== \app\models\Programs::VERIFICATION_WAIT
                            && $model->verification !== \app\models\Programs::VERIFICATION_UNDEFINED
                        ) {
                            return [
                                'class' => 'warning',
                            ];
                        }
                    },
                    'summary' => false,
                    'pjax' => true,
                    'columns' => $preparedWaitColumns,
                ]); ?>
                <?= \app\widgets\Export::widget([
                    'dataProvider' => $allNewWaitProgramsProvider,
                    'columns' => GridviewHelper::prepareColumns('programs', $waitColumns, 'wait', 'export'),
                    'group' => 'operator-wait-programs',
                    'table' => 'programs',
                    'redirectUrl' => 'operator-programs'
                ]); ?>
            </div>
            <div id="wait-after-refuse" class="tab-pane fade">
                <?= SearchFilter::widget([
                    'model' => $searchAfterRefuseWaitPrograms,
                    'action' => ['personal/operator-programs#wait-new'],
                    'data' => GridviewHelper::prepareColumns(
                        'programs',
                        $waitColumns,
                        'wait',
                        'searchFilter',
                        null
                    ),
                    'role' => UserIdentity::ROLE_OPERATOR,
                    'type' => 'wait'
                ]); ?>
                <?= GridView::widget([
                    'dataProvider' => $afterRefuseWaitProgramsProvider,
                    'filterModel' => null,
                    'rowOptions' => function ($model, $index, $widget, $grid) {
                        /** @var \app\models\Programs $model */
                        if ($model->verification === \app\models\Programs::VERIFICATION_WAIT) {
                            return ['class' => 'danger'];
                        } elseif ($model->verification !== \app\models\Programs::VERIFICATION_WAIT
                            && $model->verification !== \app\models\Programs::VERIFICATION_UNDEFINED
                        ) {
                            return [
                                'class' => 'warning',
                            ];
                        }
                    },
                    'summary' => false,
                    'pjax' => true,
                    'columns' => $preparedWaitColumns,
                ]); ?>
                <?= \app\widgets\Export::widget([
                    'dataProvider' => $allAfterRefuseWaitProgramsProvider,
                    'columns' => GridviewHelper::prepareColumns('programs', $waitColumns, 'wait', 'export'),
                    'group' => 'operator-wait-programs',
                    'table' => 'programs',
                    'redirectUrl' => 'operator-programs'
                ]); ?>
            </div>
        </div>

    </div>
    <div id="panel3" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchClosedPrograms,
            'action' => ['personal/operator-programs#panel3'],
            'data' => GridviewHelper::prepareColumns(
                'programs',
                $closedPrograms,
                'close',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'close'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $closedProgramsProvider,
            'filterModel' => false,
            'summary' => false,
            'pjax' => true,
            'columns' => $preparedClosedPrograms,
        ]); ?>
        <?= \app\widgets\Export::widget([
            'dataProvider' => $allClosedProgramsProvider,
            'columns' => GridviewHelper::prepareColumns('programs', $closedPrograms, 'close', 'export'),
            'group' => 'operator-close-programs',
            'table' => 'programs',
            'redirectUrl' => 'operator-programs'
        ]); ?>
    </div>
    <br>
    <?php
    echo ExportMenu::widget([
        'dataProvider' => $yearsAllProvider,
        'target' => '_self',
        'showColumnSelector' => false,
        'filename' => 'years',
        'dropdownOptions' => [
            'class' => 'btn btn-success',
            'label' => 'Модули',
            'icon' => false,
        ],
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
            'open',
            'previus',
            'quality_control',
            'p21z',
            'p22z',
        ],

    ]);
    echo '&nbsp;';
    echo ExportMenu::widget([
        'dataProvider' => $groupsAllProvider,
        'target' => '_self',
        'showColumnSelector' => false,
        'filename' => 'years',
        'dropdownOptions' => [
            'class' => 'btn btn-success',
            'label' => 'Группы',
            'icon' => false,
        ],
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
