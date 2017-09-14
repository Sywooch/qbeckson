<?php
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ProgramsSearch */

/* @var $dataProvider yii\data\ActiveDataProvider */

use app\helpers\GridviewHelper;
use app\models\Certificates;
use app\models\statics\DirectoryProgramDirection;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
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

$directivity = [
    'attribute' => 'direction_id',
    'value'     => 'direction.name',
    'label'     => 'Направленность',
    'type'      => SearchFilter::TYPE_DROPDOWN,
    'data'      => ArrayHelper::map(DirectoryProgramDirection::find()->all(), 'id', 'name'),
];
$age = [
    'attribute' => 'age',
    'label'     => 'Возраст',
    'type'      => SearchFilter::TYPE_INPUT,
];

$rating = [
    'attribute'     => 'rating',
    'label'         => 'Рейтинг',
    'type'          => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 100
    ]
];
$activity_ids = [
    'attribute'     => 'activity_ids',
    'label'         => 'Рейтинг',
    'type'          => SearchFilter::TYPE_SELECT2,
    'data'          => ArrayHelper::map(\app\models\statics\DirectoryProgramActivity::find()->all(), 'id', 'name'),
    'pluginOptions' => [
        'max' => 100
    ]
];

$columns = [
    $favorites,
    $name,
    $directivity,
    $activity_ids,
    $age,
    $rating,
    $zab,


];


?>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= $this->render('../../common/_select-municipality-modal') ?>
            </div>
        </div>
    </div>
    <br>

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
]); ?>
<?php
echo \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView'     => '_item',
    'layout'       => '{items}{pager}',
    'itemOptions'  => ['class' => 'col-xs-12 col-lg-6'],
    'beforeItem'   => function ($model, $key, $index, $list)
    {
        if ($index === 0) {
            return '<div class="row">';
        } elseif ((($index + 2) % 2 === 0)) {
            return '<div class="row">';
        }

        return '';
    },
    'afterItem'    => function ($model, $key, $index, $list)
    {
        /** @var $list \yii\widgets\ListView */
        if ((($index + 1) % 2 === 0 && ($index !== 0) || $index >= $list->dataProvider->getCount() - 1)) {
            return '</div>';
        }

        return '';
    }

]); ?>