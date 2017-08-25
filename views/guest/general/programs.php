<?php

use app\models\Mun;
use app\models\statics\DirectoryProgramDirection;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model \app\models\search\ProgramsSearch */
/* @var $provider \yii\data\ActiveDataProvider */

$this->title = 'Реестр образовательных программ';
$this->params['breadcrumbs'][] = $this->title;

$zab = [
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

        return $display === '' ? 'без ОВЗ' : $display;
    },
];
$name = [
    'attribute'=>'name',
    'label' => 'Наименование',
];
$year = [
    'label' => 'Число модулей',
    'value' => function ($model) {
        /** @var \app\models\Programs $model */
        return Yii::$app->i18n->messageFormatter->format(
            '{n, plural, one{# модуль} few{# модуля} many{# модулей} other{# модуля}}',
            ['n' => $model->year],
            Yii::$app->language
        );
    },
];
$hours = [
    'value' => 'countHours',
    'label' => 'Кол-во часов',
];
$directivity = [
    'attribute' => 'direction_id',
    'value' => 'direction.name',
    'label' => 'Направленность',
    'filter' => ArrayHelper::map(DirectoryProgramDirection::find()->all(), 'id', 'name'),
];
$ageGroupMin = [
    'value' => 'age_group_min',
    'label' => 'Возраст от',
];
$ageGroupMax = [
    'value' => 'age_group_max',
    'label' => 'Возраст до',
];
$rating = [
    'value' => 'rating',
    'label' => 'Рейтинг',
];
$municipality = [
    'attribute' => 'mun',
    'value' => 'municipality.name',
    'label' => 'Муниципалитет',
    'filter' => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
];
$price = [
    'label' => 'Цена*',
    'value' => function ($model) {
        /** @var \app\models\Programs $model */
        return $model->modules[0]->price;
    },
];
$normativePrice = [
    'label' => 'НС*',
    'value' => function ($model) {
        /** @var \app\models\Programs $model */
        return $model->modules[0]->normative_price;
    },
];
$actions = [
    'class' => ActionColumn::class,
    'template' => '{view}',
    'buttons' => [
        'view' => function ($url, $model, $key) {
            /** @var \app\models\Programs $model */
            return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['program', 'id' => $model->id]);
        }
    ]
];

$columns = [
    $name,
    $municipality,
    $directivity,
    //$year,
    $hours,
    $ageGroupMin,
    $ageGroupMax,
    $rating,
    $zab,
    //$price,
    //$normativePrice,
    $actions,
];

echo GridView::widget([
    'dataProvider' => $provider,
    'filterModel' => $model,
    'columns' => $columns,
    'summary' => false,
]);
