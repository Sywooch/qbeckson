<?php
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ProgramsSearch */

/* @var $dataProvider yii\data\ActiveDataProvider */

use app\helpers\GridviewHelper;
use app\models\Certificates;
use app\models\Mun;
use app\models\statics\DirectoryProgramDirection;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$js = <<<JS
$(".js-ellipsis-title").dotdotdot({
          ellipsis: '... ',
          wrap: 'word',
          height: 60
      });
JS;
$this->registerJs($js);

$this->title = 'Поиск программ';
$this->params['breadcrumbs'][] = $this->title;

$favorites = [
    'class'        => 'yii\grid\ActionColumn',
    'template'     => '{favorites}',
    'buttons'      => [
        'favorites' => function ($url, $model)
        {
            $certificates = new Certificates();
            $certificate = $certificates->getCertificates();
            $rows = (new \yii\db\Query())
                ->from('favorites')
                ->where(['certificate_id' => $certificate['id']])
                ->andWhere(['program_id' => $model->id])
                ->andWhere(['type' => 1])
                ->one();
            if (!$rows) {
                return Html::a(
                    '<span class="glyphicon glyphicon-star-empty"></span>',
                    Url::to(['/favorites/new', 'id' => $model->id]),
                    ['title' => Yii::t('yii', 'Добавить в избранное')]
                );
            } else {
                return Html::a(
                    '<span class="glyphicon glyphicon-star"></span>',
                    Url::to(['/favorites/terminate', 'id' => $model->id]),
                    ['title' => Yii::t('yii', 'Убрать из избранного')]
                );
            }
        },
    ],
    'searchFilter' => false,
];
$zab = [
    'attribute' => 'zab',
    'type'      => SearchFilter::TYPE_SELECT2,
    'data'      => $searchModel::illnesses(),
    'label'     => 'Категория детей',
    'value'     => function ($model)
    {
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
$name = [
    'attribute' => 'name',
    'label'     => 'Наименование',
];
$year = [
    'attribute' => 'year',
    'value'     => function ($model)
    {
        /** @var \app\models\Programs $model */
        return Yii::$app->i18n->messageFormatter->format(
            '{n, plural, one{# модуль} few{# модуля} many{# модулей} other{# модуля}}',
            ['n' => count($model->years)],
            Yii::$app->language
        );
    },
    'type'      => SearchFilter::TYPE_TOUCH_SPIN,
];
$hours = [
    'attribute'     => 'hours',
    'value'         => 'countHours',
    'label'         => 'Кол-во часов',
    'type'          => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 2000
    ]
];
$directivity = [
    'attribute' => 'direction_id',
    'value'     => 'direction.name',
    'label'     => 'Направленность',
    'type'      => SearchFilter::TYPE_DROPDOWN,
    'data'      => ArrayHelper::map(DirectoryProgramDirection::find()->all(), 'id', 'name'),
];
$ageGroupMin = [
    'attribute' => 'age_group_min',
    'label'     => 'Возраст от',
    'type'      => SearchFilter::TYPE_TOUCH_SPIN,
];
$ageGroupMax = [
    'attribute' => 'age_group_max',
    'label'     => 'Возраст до',
    'type'      => SearchFilter::TYPE_TOUCH_SPIN,
];
$rating = [
    'attribute'     => 'rating',
    'label'         => 'Рейтинг',
    'type'          => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 100
    ]
];
$municipality = [
    'attribute' => 'mun',
    'label'     => 'Муниципалитет',
    'type'      => SearchFilter::TYPE_DROPDOWN,
    'data'      => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
    'value'     => 'municipality.name',
];
$price = [
    'attribute'    => 'price',
    'label'        => 'Стоимость*',
    'value'        => function ($data)
    {
        $year = (new \yii\db\Query())
            ->select(['price'])
            ->from('years')
            ->where(['year' => 1])
            ->andWhere(['program_id' => $data->id])
            ->one();

        return $year['price'];
    },
    'searchFilter' => false,
];
$normativePrice = [
    'attribute'    => 'normativePrice',
    'label'        => 'НС*',
    'value'        => function ($data)
    {
        $year = (new \yii\db\Query())
            ->select(['normative_price'])
            ->from('years')
            ->where(['year' => 1])
            ->andWhere(['program_id' => $data->id])
            ->one();

        return $year['normative_price'];
    },
    'searchFilter' => false,
];
$actions = [
    'class'        => ActionColumn::class,
    'controller'   => 'programs',
    'template'     => '{view}',
    'searchFilter' => false,
];

$columns = [
    $favorites,
    $name,
    $year,
    $hours,
    $directivity,
    $ageGroupMin,
    $ageGroupMax,
    $rating,
    $zab,
    $municipality,
    $price,
    $normativePrice,
    $actions,
];


if (Yii::$app->user->can('certificate')) : ?>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= $this->render('../../common/_select-municipality-modal') ?>
            </div>
        </div>
    </div>
    <br>
<?php endif; ?>
<?= SearchFilter::widget([
    'model'  => $searchModel,
    'action' => ['personal/certificate-programs'],
    'data'   => GridviewHelper::prepareColumns(
        'programs',
        $columns,
        null,
        'searchFilter',
        null
    ),
    'role'   => UserIdentity::ROLE_CERTIFICATE,
]);

echo \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView'     => '_item'
]); ?>