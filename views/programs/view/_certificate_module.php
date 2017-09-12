<?php
/** @var $model \app\models\ProgrammeModule */

/** @var $this yii\web\View */


$canViewGroups = false;
$message = '';
$groups = $model->groups;
$value = $model;
/** @var $certificate \app\models\Certificates */
$certificate = Yii::$app->user->identity->certificate;
$organization = $model->program->organization;
$payer = $certificate->payer;
$activeContractsByProgramInPayer = $payer->getActiveContractsByProgram($model->program_id)->count();
$limit_napr = $payer->getDirectionalityCountByName($model->program->directivity);


if (count($cooperate)) {
    if (!$value->open) {
        $message = '<h4>Вы не можете записаться на программу. Зачисление закрыто.</h4>';
    } elseif (!empty($groups)) {
        if (($certificate->balance < 1 && $certificate->payer->certificate_can_use_future_balance < 1) || ($certificate->balance < 1 && $certificate->payer->certificate_can_use_future_balance > 0 && $certificate->balance_f < 1)) {
            $message = '<h4>Вы не можете записаться на программу. Нет свободных средств на сертификате.</h4>';
        } else {
            if ($organization->actual == 0) {
                $message = '<h4>Вы не можете записаться на программу. Действие организации приостановленно.</h4>';
            } else {
                if ($activeContractsByProgramInPayer >= $limit_napr && (Yii::trace($activeContractsByProgramInPayer)) || (Yii::trace($limit_napr))) {
                    $message = '<h4>Вы не можете записаться на программу. Достигнут максимальный предел числа одновременно оплачиваемых вашей уполномоченной организацией услуг по данной направленности.</h4>';
                } else {
                    if (!$organization->existsFreePlace()) {
                        $message = '<h4>Вы не можете записаться на программу. Достигнут максимальный лимит зачисления в организацию. Свяжитесь с представителем организации.</h4>';
                    } else {
                        if (!$model->program->existsFreePlace()) {
                            $message = '<h4>Достигнут максимальный лимит зачисления на обучение по программе. Свяжитесь с представителем организации.</h4>';
                        } else {
                            if ($certificate->getActiveContractsByProgram($model->program_id)->exists()) {
                                $message = '<p>Вы уже подали заявку на программу/заключили договор на обучение</p>';
                            } else {
                                $message = '<p>Вы можете записаться на программу. Выберете группу:</p>';
                                $canViewGroups = true;
                            }

                        }
                    }
                }
            }
        }
    }
}


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
        <?= $message; ?>
        <?php if ($canViewGroups): ?>
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

        <?php endif; ?>
    </div>
</div>
