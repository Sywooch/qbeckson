<?php

use app\helpers\GridviewHelper;
use app\models\UserIdentity;
use yii\grid\ActionColumn;
use app\widgets\SearchFilter;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
use app\models\Mun;

/* @var $this yii\web\View */
/* @var $searchOpenPrograms \app\models\search\ProgramsSearch */
/* @var $searchWaitPrograms \app\models\search\ProgramsSearch */
/* @var $searchClosedPrograms \app\models\search\ProgramsSearch */
/* @var $openProgramsProvider \yii\data\ActiveDataProvider */
/* @var $waitProgramsProvider \yii\data\ActiveDataProvider */
/* @var $closedProgramsProvider \yii\data\ActiveDataProvider */
/* @var $allOpenProgramsProvider \yii\data\ActiveDataProvider */
/* @var $allWaitProgramsProvider \yii\data\ActiveDataProvider */
/* @var $ProgramsallProvider \yii\data\ActiveDataProvider */
/* @var $YearsallProvider \yii\data\ActiveDataProvider */
/* @var $GroupsallProvider \yii\data\ActiveDataProvider */

$this->title = 'Программы';
$this->params['breadcrumbs'][] = $this->title;

$zab = [
    'type' => SearchFilter::TYPE_SELECT2,
    'data' => $searchOpenPrograms::illnesses(),
    'attribute' => 'zab',
    'label' => 'Категория детей',
    'value' => function ($model) {
        /** @var \app\models\Programs $model */
        $zab = explode(',', $model->zab);
        $display = '';
        if (is_array($zab)) {
            foreach ($zab as $value) {
                $display .= ', ' . $model::illnesses()[$value];
            }
            $display = mb_substr($display, 2);
        }
        if ($display === '') {
            return 'без ОВЗ';
        }

        return $display;
    }
];
$year = [
    'attribute' => 'year',
    'value' => function ($model) {
        /** @var \app\models\Programs $model */
        return Yii::$app->i18n->messageFormatter->format(
            '{n, plural, one{# модуль} few{# модуля} many{# модулей} other{# модуля}}',
            ['n' => $model->year],
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
            ['class' => 'blue', 'target' => '_blank']
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
    'attribute' => 'name',
    'label' => 'Наименование',
];
$hours = [
    'attribute' => 'hours',
    'value' => 'countHours',
    'label' => 'Кол-во часов',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
];
$directivity = [
    'attribute' => 'directivity',
    'label' => 'Направленность',
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
];
$limit = [
    'attribute' => 'limit',
    'label' => 'Лимит',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
];
$actions = [
    'class' => ActionColumn::class,
    'controller' => 'programs',
    'template' => '{view}',
    'searchFilter' => false,
];

$openColumns = [
    $name,
    $year,
    $hours,
    $directivity,
    $zab,
    $ageGroupMin,
    $ageGroupMax,
    $rating,
    $limit,
    $organization,
    $municipality,
    $actions,
];
$waitColumns = [
    $name,
    $year,
    $hours,
    $directivity,
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
    $zab,
    $ageGroupMin,
    $ageGroupMax,
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
        <a data-toggle="tab" href="#panel2">Ожидающие сертификации
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
        <?= SearchFilter::widget([
            'model' => $searchOpenPrograms,
            'action' => ['personal/operator-programs'],
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
        <?php array_pop($preparedOpenColumns) ?>
        <p class="lead">Экспорт данных:</p>
        <?= ExportMenu::widget([
            'dataProvider' => $allOpenProgramsProvider,
            'exportConfig' => [
                ExportMenu::FORMAT_EXCEL => false
            ],
            'columns' => $preparedOpenColumns,
            'filename' => 'open-programs',
            'target' => ExportMenu::TARGET_BLANK,
            'showColumnSelector' => false
        ]); ?>
        <br>
        <br>
        <p class=""><strong><span class="warning">*</span> Загрузка начнётся в новом окне и может занять некоторое время.</strong></p>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchWaitPrograms,
            'action' => ['personal/operator-programs'],
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
                if ($model->verification === 1) {
                    return ['class' => 'danger'];
                }
            },
            'summary' => false,
            'pjax' => true,
            'columns' => $preparedWaitColumns,
        ]); ?>
        <?php array_pop($preparedWaitColumns) ?>
        <p class="lead">Экспорт данных:</p>
        <?= ExportMenu::widget([
            'dataProvider' => $allWaitProgramsProvider,
            'filename' => 'wait-programs',
            'target' => ExportMenu::TARGET_BLANK,
            'showColumnSelector' => false,
            'exportConfig' => [
                ExportMenu::FORMAT_EXCEL => false
            ],
            'columns' => $preparedWaitColumns,
        ]); ?>
        <br>
        <br>
        <p class=""><strong><span class="warning">*</span> Загрузка начнётся в новом окне и может занять некоторое время.</strong></p>
    </div>
    <div id="panel3" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchClosedPrograms,
            'action' => ['personal/operator-programs'],
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
    </div>
    <br>
    <?php
    echo ExportMenu::widget([
        'dataProvider' => $ProgramsallProvider,
        'target' => '_self',
        'showColumnSelector' => false,
        'filename' => 'programs',
        'dropdownOptions' => [
            'class' => 'btn btn-success',
            'label' => 'Программы',
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
            'verification',
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
            'study',
            'last_contracts',
            'last_s_contracts',
            'last_s_contracts_rod',
            'quality_control',
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
        'dataProvider' => $GroupsallProvider,
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
