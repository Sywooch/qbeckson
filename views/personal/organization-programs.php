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
$municipality = [
    'attribute' => 'mun',
    'label' => 'Муниципалитет',
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
    'value' => 'municipality.name',
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
    'pluginOptions' => [
        'max' => 2000
    ]
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
    $municipality,
    $actions
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
    <?php
    if (Yii::$app->user->can('organizations') && Yii::$app->user->identity->organization->actual > 0) {
        echo "<p>";
        echo Html::a('Отправить программу на сертификацию', ['programs/create'], ['class' => 'btn btn-success']);
        echo "</p>";
    }
    ?>
    <div id="panel1" class="tab-pane fade in active">
        <?= SearchFilter::widget([
            'model' => $searchOpenPrograms,
            'action' => ['personal/organization-programs'],
            'data' => GridviewHelper::prepareColumns(
                'programs',
                $openColumns,
                'open',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'open'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $openProgramsProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedOpenColumns,
        ]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchWaitPrograms,
            'action' => ['personal/organization-programs'],
            'data' => GridviewHelper::prepareColumns(
                'programs',
                $waitColumns,
                'wait',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'wait'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $waitProgramsProvider,
            'filterModel' => null,
            'summary' => false,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                /** @var \app\models\Programs $model */
                if ($model->verification === 1) {
                    return ['class' => 'danger'];
                }
            },
            'pjax' => true,
            'columns' => $preparedWaitColumns,
        ]); ?>
    </div>
    <div id="panel3" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchClosedPrograms,
            'action' => ['personal/organization-programs'],
            'data' => GridviewHelper::prepareColumns(
                'programs',
                $closedPrograms,
                'close',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'close'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $closedProgramsProvider,
            'filterModel' => false,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedClosedPrograms,
        ]); ?>
    </div>
</div>
