<?php
use app\helpers\GridviewHelper;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Mun;

/* @var $this yii\web\View */
/* @var $searchPrograms \app\models\search\ProgramsSearch */
/* @var $programsProvider \yii\data\ActiveDataProvider */

$this->title = 'Программы';
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
    'label' => 'Муниципалитет',
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
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
$columns = [
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
$preparedColumns = GridviewHelper::prepareColumns('programs', $columns, 'open');

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
