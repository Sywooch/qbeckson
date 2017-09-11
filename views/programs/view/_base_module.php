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
        <?= $this->render('_base_module_controls', ['model' => $model]); ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <?= \yii\widgets\DetailView::widget([
            'options'    => [
                'tag'   => 'ul',
                'class' => 'text-info-lines'],
            'template'   => '<li {captionOptions}><strong>{label}:</strong>{value}</li>',
            'model'      => $model,
            'attributes' => array_merge([
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
                [
                    'label' => 'Адреса реализации модуля',
                    'value' => ($model->addresses ? '' : 'не указаны'),
                ],

            ],
                array_map(function ($index, $address)
                {
                    /** @var $address \app\models\OrganizationAddress */
                    return [
                        'label'          => sprintf('Адрес %d', $index + 1),
                        'value'          => $address->address,
                        'captionOptions' => ['style' => ['padding-left' => '20px']]];
                }, array_keys($model->addresses), $model->addresses))
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
