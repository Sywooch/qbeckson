<?php
use app\helpers\GridviewHelper;
use app\models\statics\DirectoryProgramDirection;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Mun;

/* @var $this yii\web\View */
/* @var $searchWaitTasks \app\models\search\ProgramsSearch */
/* @var $waitTasksProvider \yii\data\ActiveDataProvider */

$this->title = 'Муниципальные задания';
$this->params['breadcrumbs'][] = $this->title;

$zab = [
    'type' => SearchFilter::TYPE_SELECT2,
    'data' => $searchWaitTasks::illnesses(),
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
            ['target' => '_blank', 'data-pjax' => '0']
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
    'data' => $searchWaitTasks::forms(),
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
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'certificate_accounting_limit',
        'pageSummary' => false,
        'editableOptions' => [
            'asPopover' => false,
            'submitButton' => [
                'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                'class' => 'btn btn-sm btn-success',
            ],
        ],
    ],
    $actions,
];
$preparedColumns = GridviewHelper::prepareColumns('programs', $columns);
?>
<?php if ($searchWaitTasks->organization_id && $searchWaitTasks->organization) : ?>
    <p class="lead">Показаны результаты для организации: <?= $searchWaitTasks->organization; ?></p>
<?php endif; ?>
<p>
    <?= Html::a('Настроить параметры', ['/payer/matrix/params'], ['class' => 'btn btn-success']) ?>
</p>
<ul class="nav nav-tabs">
    <li class="active">
        <a data-toggle="tab" href="#panel0">Неразобранные
            <span class="badge"><?= $waitTasksProvider->getTotalCount() ?></span>
        </a>
    </li>
    <?php
    foreach ($tabs as $index => $tab) {
    ?>
        <li>
            <a data-toggle="tab" href="#panel<?= $index + 1 ?>"><?= $tab['item']->name ?>
                <span class="badge"><?= $tab['provider']->getTotalCount() ?></span>
            </a>
        </li>
    <?php
    }
    ?>
</ul>
<br/>
<div class="tab-content">
    <div id="panel0" class="tab-pane fade in active">
        <?php echo GridView::widget([
            'dataProvider' => $waitTasksProvider,
            'filterModel' => null,
            'pjax' => true,
            'columns' => $preparedColumns,
        ]); ?>
    </div>
    <?php
    foreach ($tabs as $index => $tab) {
    ?>
        <div id="panel<?= $index + 1 ?>" class="tab-pane fade in">
            <?php echo GridView::widget([
                'dataProvider' => $tab['provider'],
                'filterModel' => null,
                'pjax' => true,
                'columns' => $preparedColumns,
            ]); ?>
        </div>
    <?php
    }
    ?>
</div>
