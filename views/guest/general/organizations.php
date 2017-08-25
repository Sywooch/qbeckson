<?php

use app\models\Mun;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model \app\models\search\OrganizationSearch */
/* @var $provider \yii\data\ActiveDataProvider */

$this->title = 'Реестр поставщиков образовательных услуг';
$this->params['breadcrumbs'][] = $this->title;

$name = [
    'attribute' => 'name',
];
$municipality = [
    'attribute' => 'mun',
    'value' => 'municipality.name',
    'label' => 'Муниципалитет',
    'filter' => ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
];
$type = [
    'attribute' => 'type',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        return $model::types()[$model->type];
    },
    'filter' => $model::types(),
];
$programs = [
    'label' => 'Количество программ',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        $programsCount = $model->getPrograms()->andWhere(['programs.verification' => 2])->count();

        return (int)$programsCount > 0 ? $programsCount : '-';
    },
];
$children = [
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        $childrenCount = count(array_unique(
            $model->getChildren()->andWhere(['contracts.status' => [1]])->asArray()->all()
        ));
        return $childrenCount > 0 ? $childrenCount : '-';
    },
    'label' => 'Число обучающихся',
];
$max_child = [
    'value' => 'max_child',
    'label' => 'Лимит обучения',
];
$raiting = [
    'label' => 'Рейтинг',
    'value' => 'raiting',
];
$actual = [
    'label' => 'Актуальность',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        return $model->actual === 0 ? '-' : '+';
    },
];
$actions = [
    'class' => ActionColumn::class,
    'template' => '{view}',
    'buttons' => [
        'view' => function ($url, $model, $key) {
            /** @var \app\models\Programs $model */
            return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['organization', 'id' => $model->id]);
        }
    ]
];

$columns = [
    $name,
    $municipality,
    $type,
    $programs,
    $children,
    $max_child,
    $raiting,
    $actual,
    $actions,
];

echo GridView::widget([
    'dataProvider' => $provider,
    'filterModel' => $model,
    'columns' => $columns,
    'summary' => false,
]);
