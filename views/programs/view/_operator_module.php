<?php
/** @var $model \app\models\ProgrammeModule */

/** @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;


?>
<div class="row">
    <div class="col-xs-12">
        <h3><?= $model->fullname ?></h3>

    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <?= $this->render('_base_module_controls', ['model' => $model]); ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <?= \yii\widgets\DetailView::widget([
            'options'    => [
                'tag'   => 'ul',
                'class' => 'text-info-lines'],
            'template'   => '<li><strong>{label}:</strong>{value}</li>',
            'model'      => $model,
            'attributes' => [
                ['attribute' => 'month',
                 'label'     => 'Продолжительность (месяцев)'
                ],
                ['attribute' => 'hours',
                 'label'     => 'Продолжительность (часов)'
                ],
                ['label' => 'Наполняемость группы',
                 'value' => Yii::t('app', '{from} - {to} человек',
                     ['from' => $model->minchild, 'to' => $model->maxchild])
                ],
                [
                    'label'     => 'Квалификация руководителя кружка',
                    'attribute' => 'kvfirst',
                ],
                [
                    'attribute' => 'price',
                    'format'    => 'currency',
                ],
                [
                    'attribute' => 'normative_price',
                    'format'    => 'currency',
                ],

            ]
        ]) ?>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $model->getGroups()]),
            'summary'      => false,
            'tableOptions' => ['class' => 'theme-table'],
            'columns'      => [
                'name',
                'program.name',
                'fullSchedule:raw',
                'datestart:date',
                'datestop:date',
                'freePlaces',
                ['class'    => 'yii\grid\ActionColumn',
                 'template' => '{view}',

                 'buttons' =>
                     [
                         'view' => function ($url, $model)
                         {
                             return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/groups/contracts', 'id' => $model->id]));
                         },
                     ]
                ],

            ],
        ]); ?>
    </div>
</div>
