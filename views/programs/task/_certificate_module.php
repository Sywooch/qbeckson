<?php
use app\models\MunicipalTaskPayerMatrixAssignment;

/** @var $model \app\models\ProgrammeModule */
/** @var $this yii\web\View */

$canViewGroups = false;
$message = '';
$groups = $model->groups;
/** @var $certificate \app\models\Certificates */
$certificate = Yii::$app->user->identity->certificate;
$organization = $model->program->organization;
$payer = $certificate->payer;

if (!empty($groups)) {
    if ($organization->actual == 0) {
        $message = '<h4>Вы не можете записаться на программу. Действие организации приостановленно.</h4>';
    } else {
        if (!$model->program->canCreateMunicipalTaskContract($certificate)) {
            $message = '<h4>Вы не можете записаться в группу из-за превышения лимитов на зачисление.</h4>';
        } else {
            $message = '<p>Вы можете записаться на программу. Выберете группу:</p>';
            $canViewGroups = true;
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
        <?= $message; ?>
        <?php if ($canViewGroups): ?>
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
                                    return 'До 1 февраля запись не доступна.';
//                                        \yii\helpers\Html::a('Выбрать', ['/municipal-task-contract/create', 'groupId' => $model->id], [
//                                        'class' => 'btn btn-success',
//                                        'title' => 'Выбрать',
//                                    ]);
                                },
                            ]
                    ],
                ],
            ]); ?>
        <?php endif; ?>
    </div>
</div>
