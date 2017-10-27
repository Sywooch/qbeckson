<?php

use app\helpers\GridviewHelper;
use app\models\Mun;
use app\models\statics\DirectoryProgramDirection;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchPrograms \app\models\search\ProgramsSearch */
/* @var $programsProvider \yii\data\ActiveDataProvider */

$this->title = 'Программы';
$this->params['breadcrumbs'][] = $this->title;

$zab = [
    'type' => SearchFilter::TYPE_SELECT2,
    'data' => $searchPrograms::illnesses(),
    'attribute' => 'zab',
    'label'     => 'Категория детей',
    'value'     => function ($model)
    {
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
            ['target' => '_blank', 'data-pjax' => false]
        );
    },
];
$municipality = [
    'attribute' => 'mun',
    'label' => 'Муниципалитет',
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => ArrayHelper::map(Mun::find()->andWhere([
        'operator_id' => Yii::$app->operator->identity->id
    ])->all(), 'id', 'name'),
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
$actions = [
    'class' => ActionColumn::class,
    'controller' => 'programs',
    'template' => '{view}',
    'searchFilter' => false,
];
$columns = [
    $name,
    $year,
    $hours,
    $form,
    $directivity,
    $zab,
    $ageGroupMin,
    $ageGroupMax,
    $rating,
    $limit,
    $organization,
    $municipality,
    [
        'attribute' => 'organization_id',
        'type' => SearchFilter::TYPE_HIDDEN,
    ],
    $actions,
];
$preparedColumns = GridviewHelper::prepareColumns('programs', $columns, null);
?>
<?php if ($searchPrograms->organization_id && $searchPrograms->organization) : ?>
    <p class="lead">Показаны результаты для организации: <?= $searchPrograms->organization; ?></p>
<?php endif; ?>
<?php
echo SearchFilter::widget([
    'model' => $searchPrograms,
    'action' => ['personal/payer-programs'],
    'data' => GridviewHelper::prepareColumns(
        'programs',
        $columns,
        null,
        'searchFilter',
        null
    ),
    'role' => UserIdentity::ROLE_PAYER,
]);
echo GridView::widget([
    'dataProvider' => $programsProvider,
    'filterModel' => null,
    'pjax' => true,
    'columns' => $preparedColumns,
]);
?>
