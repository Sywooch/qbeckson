<?php
/** @var $model \app\models\module\CertificateAccessModuleDecorator */
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
            'options' => [
                'tag' => 'ul',
                'class' => 'text-info-lines'],
            'template' => '<li><strong>{label}:</strong>{value}</li>',
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

            ], call_user_func(function ($model): array {
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
            }, $model))
        ]) ?>

        <?php if ($model->certificateCanEnlistmentToProgram()): ?>
            <?= $model->getLastMessage(); ?>
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
                    ['class' => 'yii\grid\ActionColumn',
                        'header' => 'Действия',
                        'template' => '{permit}',
                        'buttons' =>
                            [
                                'permit' => function ($url, $model) {
                                    /** @var $identity \app\models\UserIdentity */
                                    $identity = Yii::$app->user->identity;
                                    if ($model->freePlaces && $identity->certificate->actual) {
                                        return \yii\helpers\Html::a(
                                            'Выбрать',
                                            \yii\helpers\Url::to(['/contracts/request', 'groupId' => $model->id]),
                                            [
                                                'class' => 'btn btn-success',
                                                'title' => 'Выбрать'
                                            ]
                                        );
                                    }

                                    return \app\components\widgets\ButtonWithInfo::widget([
                                        'label' => 'Выбрать',
                                        'message' => !$model->freePlaces
                                            ? 'Нет свободных мест'
                                            : 'Ваш сертификат заморожен',
                                        'options' => ['disabled' => 'disabled',
                                            'class' => 'btn btn-default',
                                            'style' => ['color' => '#333'],]
                                    ]);
                                },

                            ]
                    ],

                ],
            ]); ?>
        <?php else: ?>
            <?= $model->getLastMessage(); ?>
        <?php endif; ?>
    </div>
</div>
