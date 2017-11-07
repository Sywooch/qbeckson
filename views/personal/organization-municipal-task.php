<?php

use app\models\statics\DirectoryProgramDirection;
use app\helpers\GridviewHelper;
use app\models\UserIdentity;
use yii\grid\ActionColumn;
use app\widgets\SearchFilter;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Mun;

$this->title = 'Муниципальное задание';
$this->params['breadcrumbs'][] = $this->title;

$zab = [
    'type' => SearchFilter::TYPE_SELECT2,
    'data' => $searchPrograms::illnesses(),
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
    'attribute' => 'direction_id',
    'value' => 'direction.name',
    'label' => 'Направленность',
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => ArrayHelper::map(DirectoryProgramDirection::find()->all(), 'id', 'name'),
];
$form = [
    'attribute' => 'form',
    'value' => function ($model) {
        return $model::forms()[$model->form];
    },
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => $searchPrograms::forms(),
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
    'buttons' => [
        'view' => function ($url, $model, $key) {
            return '<a href="' . Url::to(['/programs/view-task', 'id' => $model->id]) . '" title="Просмотр" aria-label="Просмотр" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>';
        },
    ],
    'searchFilter' => false,
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
    $municipality,
    $actions,
];

$preparedOpenColumns = GridviewHelper::prepareColumns('programs', $openColumns, 'open');

?>
<ul class="nav nav-tabs">
    <li class="active">
        <a data-toggle="tab" href="#panel1">Реестр заданий
            <span class="badge"><?= $programsProvider->getTotalCount() ?></span>
        </a>
    </li>
</ul>
<br>
<div class="tab-content">
    <?php
    if (Yii::$app->user->can('organizations') && Yii::$app->user->identity->organization->actual > 0) {
        echo "<p>";
        echo Html::a('Добавить задание', ['programs/create', 'isTask' => 1], ['class' => 'btn btn-success']);
        echo "</p>";
    }
    ?>
    <div id="panel1" class="tab-pane fade in active">
        <?= SearchFilter::widget([
            'model' => $searchPrograms,
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
            'dataProvider' => $programsProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedOpenColumns,
        ]); ?>
    </div>
</div>
