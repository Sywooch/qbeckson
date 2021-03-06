<?php

use app\helpers\GridviewHelper;
use app\models\Organization;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Группы';
$this->params['breadcrumbs'][] = 'Группы';
/* @var $this yii\web\View */
/* @var $searchGroups \app\models\search\GroupsSearch */
/* @var $groupsProvider \yii\data\ActiveDataProvider */


$columns = [
    [
        'attribute' => 'name',
    ],
    [
        'attribute' => 'programName',
        'value'     => function ($model)
        {
            return Html::a(
                $model->program->name,
                ['programs/view', 'id' => $model->program->id],
                ['target' => '_blank', 'data-pjax' => '0']
            );
        },
        'label'     => 'Программа',
        'format'    => 'raw'
    ],

    ['attribute' => 'fullSchedule',
     'format'    => 'raw'],


    [
        'attribute' => 'datestart',
        'format'    => 'date',
        'label'     => 'Начало',
    ],
    [
        'attribute' => 'datestop',
        'format'    => 'date',
        'label'     => 'Конец',
    ],
    [
        'attribute' => 'studentsCount',
        'label'     => 'Обучающихся',
        'value'     => function ($model)
        {
            /** @var \app\models\Groups $model */
            return $model->getContracts()->andWhere(['status' => 1])->count();
        }
    ],
    [
        'attribute' => 'requestsCount',
        'label'     => 'Заявок',
        'value'     => function ($model)
        {
            /** @var \app\models\Groups $model */
            return $model->getContracts()->andWhere(['status' => [0, 3]])->count();
        }
    ],
    [
        'attribute' => 'placesCount',
        'label'     => 'Мест',
        'value'     => function ($model)
        {
            /** @var \app\models\Groups $model */
            return $model->module->maxchild - $model->getContracts()->andWhere(['status' => [0, 1, 3]])->count();
        }
    ],
    [
        'class'        => ActionColumn::class,
        'controller'   => 'groups',
        'template'     => '{contracts}',
        'buttons'      => [
            'contracts' => function ($url, $model)
            {
                return Html::a(
                    $model->isActive ? '<span class="glyphicon glyphicon-eye-open"></span>' : '<span class="glyphicon glyphicon-eye-close"></span>',
                    $model->isActive ? Url::to(['groups/contracts', 'id' => $model->id]) : '#',
                    $model->isActive ? ['title' => 'Просмотреть договоры'] : ['title' => 'Группа находится в архиве']
                );
            }
        ],
        'searchFilter' => false,
    ]
];
?>

<?php
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
$organizations = new Organization();
$organization = $organizations->getOrganization();
/*if ($roles['organizations'] and $organization['actual'] !== 0) {
    echo '<p>';
    echo Html::a('Добавить группу', ['/groups/create'], ['class' => 'btn btn-success']);
    echo '</p>';
}*/
?>

<?= SearchFilter::widget([
    'model'  => $searchGroups,
    'action' => ['personal/organization-groups'],
    'data'   => array_merge(GridviewHelper::prepareColumns(
        'groups',
        $columns,
        null,
        'searchFilter',
        null
    ), [['type' => 'drop-down', 'attribute' => 'status', 'data' => [\app\models\Groups::STATUS_ARCHIVED => 'В архиве',
                                                                    \app\models\Groups::STATUS_ACTIVE   => 'Активна']]]),
    'role'   => UserIdentity::ROLE_ORGANIZATION,
]); ?>
<?= GridView::widget([
    'dataProvider' => $groupsProvider,
    'filterModel'  => null,
    'rowOptions'   => function ($model, $key, $index, $grid)
    {
        if (!$model->isActive) {
            return ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Группа находится в архиве'];
        }
    },
    'summary'      => false,
    'columns'      => GridviewHelper::prepareColumns(
        'groups',
        $columns
    ),
]);
