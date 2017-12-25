<?php
/** @var $model \app\models\ProgrammeModule */
/** @var $this yii\web\View */

/** @var $cooperate Cooperate */

use app\models\Cooperate;
use app\models\Groups;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

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
                if ($activeContractsByProgramInPayer >= $limit_napr) {
                    $message = '<h4>Вы не можете записаться на программу. Достигнут максимальный предел числа одновременно оплачиваемых вашей уполномоченной организацией услуг по данной направленности.</h4>';
                } else {
                    if (!$organization->existsFreePlace()) {
                        $message = '<h4>Вы не можете записаться на программу. Достигнут максимальный лимит зачисления в организацию. Свяжитесь с представителем организации.</h4>';
                    } else {
                        if (!$model->program->existsFreePlace()) {
                            $message = '<h4>Достигнут максимальный лимит зачисления на обучение по программе. Свяжитесь с представителем организации.</h4>';
                        } else {
                            if ($certificate->getContractByYearId($model->id)->exists()) {
                                $message = '<p>Вы уже подали заявку на модуль/заключили договор на обучение</p>';
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

            ], call_user_func(function ($model): array
            {
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
                                 /** @var $model Groups */
                                 if ($model->freePlaces && $identity->certificate->actual) {
                                         if ($identity->certificate->contractExistInAnotherModuleOfProgram($model->module->program_id, $model->year_id)) {
                                             return Html::button('Выбрать', [
                                                 'class' => 'btn btn-success',
                                                 'onClick' => '$("#ask-for-contract-prolongation-modal-' . $model->year_id . '").modal(); $("#contract-request-link-' . $model->year_id . '").prop("href", \'' . Url::to(['/contracts/request', 'groupId' => $model->id]) . '\');',
                                                 'data' => ['url' => Url::to(['/contracts/request', 'groupId' => $model->id])]
                                             ]);
                                         } else {
                                             return Html::a('Выбрать',
                                                 Url::to(['/contracts/request', 'groupId' => $model->id]),
                                                 [
                                                     'class' => 'btn btn-success',
                                                     'title' => 'Выбрать'
                                                 ]);
                                         }
                                 }

                                 return \app\components\widgets\ButtonWithInfo::widget([
                                     'label' => 'Выбрать',
                                     'message' => !$model->freePlaces ? 'Нет свободных мест' : 'Ваш сертификат заморожен',
                                     'options' => ['disabled' => 'disabled',
                                         'class' => 'btn btn-default',
                                         'style' => ['color' => '#333'],]
                                 ]);
                             },

                         ]
                    ],

                ],
            ]); ?>

        <?php endif; ?>
    </div>
</div>

<?php Modal::begin([
    'id' => 'ask-for-contract-prolongation-modal-' . $model->id,
    'header' => 'Подача новой заявки'
]) ?>

<p>Выбранная образовательная услуга предполагает продолжение освоение программы,
    по которой уже осуществлялось обучение ребенка. Вы уверены, что хотите подать новую отдельную заявку,
    а не дождаться пролонгации договора?</p>

<?= Html::a('Да, подать новую заявку',
    '',
    [
        'id' => 'contract-request-link-' . $model->id,
        'class' => 'btn btn-success',
        'title' => 'Выбрать'
    ]); ?>

<?php Modal::begin([
    'header' => 'Дождаться пролонгации договора',
    'toggleButton' => [
        'label' => 'Нет, дождаться пролонгации договора',
        'class' => 'btn btn-primary',
    ],
    'clientOptions' => ['backdrop' => false]
]) ?>
<p>Поставщик услуг по завершению срока действия договора самостоятельно сформирует оферту.
    Если Вы в течение установленного срока не отзовете ее – договор на продолжение обучения вступит в силу</p>
<?= Html::button('Закрыть', ['class' => 'btn btn-primary', 'onClick' => '$(".modal").modal("hide")']) ?>
<?php Modal::end() ?>
<?php Modal::end() ?>
