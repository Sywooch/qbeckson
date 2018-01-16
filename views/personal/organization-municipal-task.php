<?php

/* @var $waitProgramsProvider \yii\data\ActiveDataProvider */
/* @var $deniedProgramsProvider \yii\data\ActiveDataProvider */

/* @var $draftProgramsProvider \yii\data\ActiveDataProvider */

use app\helpers\GridviewHelper;
use app\models\Mun;
use app\models\statics\DirectoryProgramDirection;
use app\widgets\SearchFilter;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Муниципальное задание';
$this->params['breadcrumbs'][] = $this->title;

$zab = [
    'type' => SearchFilter::TYPE_SELECT2,
    'data' => \app\models\Programs::illnesses(),
    'attribute' => 'zab',
    'label' => 'Категория детей',
    'value' => function ($model) {
        /** @var \app\models\Programs $model */
        return $model->illnessesList;
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
        return \app\models\Programs::forms()[$model->form];
    },
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => \app\models\Programs::forms(),
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
$preparedDraftColumns = GridviewHelper::prepareColumns('programs', $openColumns, 'open');
?>
<ul class="nav nav-tabs">
    <?php
    $i = 0;
    foreach ($tabs as $index => $tab) {
        ?>
        <li <?= !$i++ ? 'class="active"' : '' ?>>
            <a data-toggle="tab" href="#panel<?= $index + 1 ?>"><?= $tab['item']->name ?>
                <span class="badge"><?= $tab['provider']->getTotalCount() ?></span>
            </a>
        </li>
        <?php
    }
    ?>
    <li>
        <a data-toggle="tab" href="#panel-e-0">Ожидающие
            <span class="badge"><?= $waitProgramsProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel-e-1">Невошедшие в реестр
            <span class="badge"><?= $deniedProgramsProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel-e-2">Черновики
            <span class="badge"><?= $draftProgramsProvider->getTotalCount() ?></span>
        </a>
    </li>
</ul>
<br>
<?php
if (!Yii::$app->user->identity->organization->suborderPayer) {
    echo \app\components\widgets\ButtonWithInfo::widget([
        'label' => 'Добавить программу',
        'message' => 'Невозможно. Не установлена подведомственность ни с одной организацией.',
        'options' => ['disabled' => 'disabled',
            'class' => 'btn btn-theme',]
    ]);
    echo '<br /><br />';
} elseif (Yii::$app->user->can('organizations') && Yii::$app->user->identity->organization->actual > 0) {
    echo "<p>";
    echo Html::a('Добавить программу', ['programs/create', 'isTask' => 1], ['class' => 'btn btn-success']);
    echo "</p>";
}
?>
<div class="tab-content">
    <?php
    $i = 0;
    foreach ($tabs as $index => $tab) {
        ?>
        <div id="panel<?= $index + 1 ?>" class="tab-pane fade in <?= !$i++ ? 'active' : '' ?>">
            <?php echo GridView::widget([
                'dataProvider' => $tab['provider'],
                'filterModel' => null,
                'pjax' => true,
                'columns' => $preparedOpenColumns,
            ]); ?>
        </div>
        <?php
    }
    ?>
    <div id="panel-e-0" class="tab-pane fade in">
        <?= GridView::widget([
            'dataProvider' => $waitProgramsProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedOpenColumns,
        ]); ?>
    </div>
    <div id="panel-e-1" class="tab-pane fade in">
        <?= GridView::widget([
            'dataProvider' => $deniedProgramsProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedOpenColumns,
        ]); ?>
    </div>
    <div id="panel-e-2" class="tab-pane fade in">
        <?= GridView::widget([
            'dataProvider' => $draftProgramsProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedDraftColumns,
        ]); ?>
    </div>
</div>
