<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'Предварительные записи';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= GridView::widget([
    'dataProvider' => $FavoritesProvider,
    'filterModel' => $searchFavorites,
    'columns' => [
        'certificate.fio_child',
        'certificate.number',
        [
            'attribute' => 'program.name',
            'format' => 'raw',
            'value' => function ($data) {

                return Html::a($data->program->name, Url::to(['/programs/view', 'id' => $data->program->id]), ['class' => 'blue', 'target' => '_blank']);
            },
        ],
        'year.year',
        ['class' => 'yii\grid\ActionColumn',
            'controller' => 'certificates',
            'template' => '{view}',
            'buttons' => [
                'view' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/certificates/view', 'id' => $model->certificate->id]));
                },
            ],
        ],
    ],
]); ?>
