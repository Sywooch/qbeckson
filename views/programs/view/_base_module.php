<?php
/** @var $model \app\models\ProgrammeModule */

/** @var $this yii\web\View */

?>
<div class="row">
    <div class="col-xs-12">
        <h3><?= $model->fullname ?></h3>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="btn-row">
            <button class="btn btn-theme">Создать группу</button>
            <button class="btn btn-theme">Записаться</button>
            <button class="btn btn-theme">Установить цену</button>
        </div>
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
                 'header'   => 'Действия',
                 'template' => '{permit}',
                 'buttons'  =>
                     [
                         'permit' => function ($url, $model)
                         {
                             /** @var $identity \app\models\UserIdentity */
                             $identity = Yii::$app->user->identity;
                             if ($model->freePlaces && $identity->certificate->actual) {

                                 return \yii\helpers\Html::a('Выбрать',
                                     \yii\helpers\Url::to(['/contracts/request', 'groupId' => $model->id]),
                                     [
                                         'class' => 'btn btn-success',
                                         'title' => 'Выбрать'
                                     ]);
                             }

                             return false;
                         },

                     ]
                ],

            ],
        ]); ?>
    </div>
</div>
