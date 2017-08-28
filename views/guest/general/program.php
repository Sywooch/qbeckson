<?php

use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this \yii\web\View */
/* @var $model \app\models\Programs */

$this->title = 'Просмотр программы ' . $model->name;
$this->params['breadcrumbs'][] = $this->title;

$programAttributes = [
    [
        'attribute' => 'organization.name',
        'format' => 'raw',
        'value' => Html::a(
            $model->organization->name,
            Url::to(['guest/general/organization', 'id' => $model->organization->id]),
            ['class' => 'blue', 'target' => '_blank']
        ),
    ],
    'directivity',
    [
        'attribute' => 'activities',
        'value' => function ($model) {
            /** @var \app\models\Programs $model */
            if ($model->activities) {
                return implode(', ', ArrayHelper::getColumn($model->activities, 'name'));
            }

            return $model->vid;
        }
    ],
    'limit',
    [
        'label' => 'Возраст детей',
        'value' => 'с ' . $model->age_group_min . ' лет до ' . $model->age_group_max . ' лет',
    ],
    [
        'attribute' => 'zab',
        'label' => 'Категория детей',
        'value' => $model->zabName($model->zab, $model->ovz),
    ],
    'task:ntext',
    'annotation:ntext',
    [
        'attribute' => 'link',
        'format' => 'raw',
        'value' => Html::a(
            '<span class="glyphicon glyphicon-download-alt"></span>',
            ['/' . $model->link]
        ),
    ],
    [
        'attribute' => 'mun',
        'value' => $model->municipality->name,
        'format' => 'raw',
    ],
    [
        'attribute' => 'ground',
        'value' => $model->groundName($model->ground),
    ],
    [
        'attribute' => 'norm_providing',
        'label' => 'Нормы оснащения',
    ],
];
$programSummaryAttributes =  [
    [
        'attribute' => 'year',
        'value' => $model->year,
    ],
    [
        'label' => 'Общая продолжительность, часов',
        'attribute' => 'countHours',
    ],
    [
        'label' => 'Общая продолжительность, месяцев',
        'attribute' => 'countMonths',
    ]
];
?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="well">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => $programAttributes
            ]) ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => $programSummaryAttributes
            ]) ?>
        </div>
        <?php foreach ($model->modules as $module) : ?>
            <?php $moduleAttributes = [
                'price',
                'normative_price',
                'month',
                [
                    'label' => 'Часов по учебному плану',
                    'attribute' => 'hours',
                ],
                [
                    'label' => 'Наполняемость группы',
                    'value' => 'от ' . $module->minchild . ' до ' . $module->maxchild,
                ],
                [
                    'label' => 'Квалификация руководителя кружка',
                    'attribute' => 'kvfirst',
                ],
            ]; ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <p class="lead"><?= $module->getFullname() ?></p>
                    <?= DetailView::widget([
                        'model' => $module,
                        'attributes' => $moduleAttributes
                    ]) ?>
                    <?php $groupColumns = [
                        'name',
                        [
                            'label' => 'Расписание',
                            'value' => function ($model) {
                                /** @var \app\models\Groups $model */
                                return $model->formatClasses();
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'datestart',
                            'format' => 'date',
                            'label' => 'Начало',
                        ],
                        [
                            'attribute' => 'datestop',
                            'format' => 'date',
                            'label' => 'Конец',
                        ],
                        [
                            'label' => 'Мест',
                            'value' => function ($model) {
                                $contract = (new \yii\db\Query())
                                    ->select(['id'])
                                    ->from('contracts')
                                    ->where(['status' => [0, 1, 3]])
                                    ->andWhere(['group_id' => $model->id])
                                    ->count();
                                $years = (new \yii\db\Query())
                                    ->select(['maxchild'])
                                    ->from('years')
                                    ->where(['id' => $model->year_id])
                                    ->one();
                                return $years['maxchild'] - $contract;
                            }
                        ]
                    ]; ?>
                    <?= GridView::widget([
                        'dataProvider' => new ArrayDataProvider([
                            'allModels' => $module->groups,
                        ]),
                        'columns' => $groupColumns,
                        'summary' => false,
                    ]) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
