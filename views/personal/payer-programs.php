<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Mun;

/* @var $this yii\web\View */

$this->title = 'Программы';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= GridView::widget([
    'dataProvider' => $ProgramsProvider,
    'filterModel' => $searchPrograms,
    'resizableColumns' => true,
    'pjax' => true,
    'summary' => false,
    'columns' => [
        [
            'attribute' => 'name',
            'label' => 'Наименование',
        ],
        [
            'attribute' => 'year',
            'value' => function ($data) {
                return Yii::$app->i18n->messageFormatter->format(
                    '{n, plural, one{# модуль} few{# модуля} many{# модулей} other{# модуля}}',
                    ['n' => $data->year],
                    Yii::$app->language
                );
            }
        ],
        'countHours',
        [
            'attribute' => 'directivity',
            'label' => 'Направленность',
        ],

        [
            'attribute' => 'zab',
            'label' => 'Категория детей',
            'value' => function ($data) {
                $zab = explode(',', $data->zab);
                $display = '';
                foreach ($zab as $value) {
                    if ($value == 1) {
                        $display = $display . ', глухие';
                    }
                    if ($value == 2) {
                        $display = $display . ', слабослышащие и позднооглохшие';
                    }
                    if ($value == 3) {
                        $display = $display . ', слепые';
                    }
                    if ($value == 4) {
                        $display = $display . ', слабовидящие';
                    }
                    if ($value == 5) {
                        $display = $display . ', нарушения речи';
                    }
                    if ($value == 6) {
                        $display = $display . ', фонетико-фонематическое нарушение речи';
                    }
                    if ($value == 7) {
                        $display = $display . ', нарушение опорно-двигательного аппарата';
                    }
                    if ($value == 8) {
                        $display = $display . ', задержка психического развития';
                    }
                    if ($value == 9) {
                        $display = $display . ', расстройство аутистического спектра';
                    }
                    if ($value == 10) {
                        $display = $display . ', нарушение интеллекта';
                    }
                }
                if ($display == '') {
                    return 'без ОВЗ';
                } else {
                    return mb_substr($display, 2);
                }

            }
        ],
        [
            'attribute' => 'age_group_min',
            'label' => 'Возраст от',
        ],
        [
            'attribute' => 'age_group_max',
            'label' => 'Возраст до',
        ],

        [
            'attribute' => 'rating',
            'label' => 'Рейтинг',
        ],
        [
            'attribute' => 'limit',
            'label' => 'Лимит',
        ],

        [
            'attribute' => 'organization',
            'label' => 'Организация',
            'format' => 'raw',
            'value' => function ($data) {

                $organization = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('organization')
                    ->where(['name' => $data->organization->name])
                    ->one();


                return Html::a($data->organization->name, Url::to(['/organization/view', 'id' => $organization['id']]), ['class' => 'blue', 'target' => '_blank']);
            },
        ],
        [
            'attribute' => 'mun',
            'label' => 'Муниципалитет',
            'filter' => ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
            'value' => function ($data) {
                $mun = (new \yii\db\Query())
                    ->select(['name'])
                    ->from('mun')
                    ->where(['id' => $data->mun])
                    ->one();

                return $mun['name'];
            },
        ],
        ['class' => 'yii\grid\ActionColumn',
            'controller' => 'programs',
            'template' => '{view}',
        ],
    ],
]); ?>
