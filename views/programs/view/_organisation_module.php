<?php
/** @var $model \app\models\ProgrammeModule */

/** @var $this yii\web\View */

use app\models\AutoProlongation;
use app\models\Groups;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\helpers\Url;

$organizationId = \Yii::$app->user->identity->organization->id;

?>
<div class="row">
    <div class="col-xs-12">
        <h3><?= $model->fullname ?></h3>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <?php
        if ($model->verification !== \app\models\ProgrammeModule::VERIFICATION_DONE) {
            echo \yii\bootstrap\Alert::widget(
                [
                    'options' => [
                        'class' => 'alert-danger',
                    ],
                    'body' => 'Модуль не сертифицирован',
                ]
            );
        }
        ?>
        <?= $this->render('_base_module_controls', ['model' => $model]); ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">

        <?= \yii\widgets\DetailView::widget([
            'options' => [
                'tag' => 'ul',
                'class' => 'text-info-lines'],
            'template' => '<li {captionOptions}><strong>{label}:</strong>{value}</li>',
            'model' => $model,
            'attributes' => array_merge([
                ['attribute' => 'month',
                    'label' => 'Продолжительность (месяцев)'
                ],
                ['attribute' => 'hours',
                    'label' => 'Продолжительность (часов)'
                ],
                ['label' => 'Наполняемость группы',
                    'value' => Yii::t('app', '{from} - {to} человек',
                        ['from' => $model->minchild, 'to' => $model->maxchild])
                ],
                [
                    'label' => 'Квалификация руководителя кружка',
                    'attribute' => 'kvfirst',
                ],
                [
                    'attribute' => 'price',
                    'format' => 'currency',
                ],
                [
                    'attribute' => 'normative_price',
                    'format' => 'currency',
                ],
                [
                    'label' => 'Адреса реализации модуля',
                    'value' => ($model->addresses ? '' : 'не указаны'),
                ],

            ],
                array_map(function ($index, $address) {
                    /** @var $address \app\models\OrganizationAddress */
                    return [
                        'label' => sprintf('Адрес %d', $index + 1),
                        'value' => $address->address,
                        'captionOptions' => ['style' => ['padding-left' => '20px']]];
                }, array_keys($model->addresses), $model->addresses),
                call_user_func(function ($model): array {
                    /**@var $model \app\models\ProgrammeModule */
                    $result = [];
                    if ($model->hoursindivid) {
                        array_push($result, ['attribute' => 'hoursindivid']);
                    }
                    if ($model->hoursdop) {
                        array_push($result, ['attribute' => 'hoursdop']);
                        if ($model->kvdop) {
                            array_push($result, ['attribute' => 'kvdop']);
                        }
                    }

                    return $result;
                }, $model)
            )
        ]) ?>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $model->getGroups()]),
            'summary' => false,
            'tableOptions' => ['class' => 'theme-table'],
            'columns' => [
                'name',
                'program.name',
                'fullSchedule:raw',
                'datestart:date',
                'datestop:date',
                'freePlaces',
                [
                    'class' => ActionColumn::className(),
                    'header' => 'Перевод',
                    'template' => '{auto-prolong}',
                    'buttons' => [
                        'auto-prolong' => function ($url, $group, $key) use ($organizationId) {
                            /** @var $group Groups */
                            return Html::button('перевод', [
                                'class' => 'btn btn-primary new-group-auto-prolongation-button',
                                'disabled' => !AutoProlongation::canGroupBeAutoProlong($organizationId, $group->id),
                                'title' => !AutoProlongation::canGroupBeAutoProlong($organizationId, $group->id) ? 'Невозможно перевести детей из этой группы' : 'Осуществить перевод на следующий модуль по программе',
                                'data' => [
                                    'url' => '/programs/new-group-auto-prolongation',
                                    'group-id' => $group->id,
                                    'modal' => 'new-group-auto-prolongation-modal',
                                ]
                            ]);
                        }
                    ]
                ],
                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',

                    'buttons' =>
                        [
                            'view' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/groups/contracts', 'id' => $model->id]));

                            },
                        ]
                ],

            ],
        ]); ?>
    </div>
</div>
