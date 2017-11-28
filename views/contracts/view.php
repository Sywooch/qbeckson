<?php

use app\components\widgets\modalCheckLink\ModalCheckLink;
use app\models\Certificates;
use app\models\Contracts;
use app\models\UserIdentity;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */
/* @var $completenessQuery \yii\db\ActiveQuery */

if ($model->status == Contracts::STATUS_ACTIVE || $model->status == Contracts::STATUS_CLOSED || $model->status == Contracts::STATUS_REFUSED) {
    $this->title = 'Просмотр договора № ' . $model->number . ' от ' . Yii::$app->formatter->asDate($model->date);
} elseif ($model->status == Contracts::STATUS_REQUESTED) {
    $this->title = 'Просмотр заявки';
} elseif ($model->status == Contracts::STATUS_ACCEPTED) {
    $this->title = 'Просмотр оферты';
}

if (Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)) {
    $this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/operator-contracts']];
}
if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
    $this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/organization-contracts']];
}

if (Yii::$app->user->can(UserIdentity::ROLE_PAYER)) {
    $this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/payer-contracts']];
}

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-view col-md-8 col-md-offset-2">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    $cert = $model->certificate;


    if ($model->status == Contracts::STATUS_REQUESTED
        && (!Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)
            && !Yii::$app->user->can(UserIdentity::ROLE_PAYER)
        )) {
        echo '<div class="alert alert-warning">Ваша заявка находится на рассмотрении поставщика образовательных услуг. Дождитесь оферты от поставщика на заключения договора. Заявка будет переведена в раздел "Ожидающие договоры" автоматически после получения оферты.</div>';
    } elseif ($model->status == Contracts::STATUS_ACCEPTED) {
        echo '<div class="alert alert-warning">Для того, чтобы завершить заключение договора напишите заявление на обучение в соответствии с <a href="' . Url::to(['application-pdf', 'id' => $model->id]) . '">представленным образцом заявления</a>. Вы можете распечатать образец или переписать от руки на листе бумаги. После написания заявления отнесите его лично или передайте с ребенком поставщику образовательных услуг.</div>';
    } elseif ($model->status === Contracts::STATUS_REFUSED) {
        $msg = array_pop($model->informs)->text;
        $msg = $msg ?? 'причина отклонения не указана';
        echo '<div class="alert alert-warning">' . $msg . '</div>';
    }

    if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)
        || Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)
        || Yii::$app->user->can(UserIdentity::ROLE_PAYER)) {
        echo DetailView::widget([
            'model' => $model->organization,
            'attributes' => [
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => Html::a($model->organization->name,
                        Url::to(['/organization/view', 'id' => $model->organization->id]), ['class' => 'blue', 'target' => '_blank']),
                ],

            ],
        ]);
    }

    if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)
        || Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)
        || Yii::$app->user->can(UserIdentity::ROLE_PAYER)) {
        echo DetailView::widget([
            'model' => $model->certificate,
            'attributes' => [
                'fio_child',
                [
                    'attribute' => 'fio_parent',
                    'label' => 'Заказчик по договору',
                ],
                [
                    'attribute' => 'number',
                    'format' => 'raw',
                    'value' => Html::a($model->certificate->number,
                        Url::to(['/certificates/view', 'id' => $model->certificate->id]),
                        ['class' => 'blue', 'target' => '_blank']),
                ],
            ],
        ]);
    }
    ?>

    <?php
    if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)
        || Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)
        || Yii::$app->user->can(UserIdentity::ROLE_ADMINISTRATOR)
        || Yii::$app->user->can(UserIdentity::ROLE_MONITOR)
    ) {
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'payers.name',
                    'label' => 'Наименование плательщика',
                    'format' => 'raw',
                    'value' => Html::a($model->payers->name, Url::to(['/payers/view', 'id' => $model->payers->id]), ['class' => 'blue', 'target' => '_blank']),
                ]
            ],
        ]);
    }
    ?>

    <?= DetailView::widget([
        'model' => $model->program,
        'attributes' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => Html::a($model->program->name, Url::to(['/programs/view', 'id' => $model->program->id]), ['class' => 'blue', 'target' => '_blank']),
            ],
            [
                'label' => 'Модуль',
                'value' => $model->year->fullname,
            ],
        ],
    ]) ?>

    <?php
    if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
        echo DetailView::widget([
            'model' => $model->group,
            'attributes' => [
                'name',
                'module.mainAddress.address',
                'fullSchedule:raw',

            ],
        ]);
    } else {
        echo DetailView::widget([
            'model' => $model->group,
            'attributes' => [
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => Html::a($model->group->name,
                        Url::to(['/groups/contracts', 'id' => $model->group->id]),
                        ['class' => 'blue', 'target' => '_blank']),
                ],
            ],
        ]);
    } ?>

    <?php
    if ($model->wait_termnate == 1 && $model->status != Contracts::STATUS_CLOSED) {
        echo '<h3>Ожидается расторжение договора с первого числа следующего месяца</h3>';

        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'terminator_user',
                    'value' => $model->terminator_user == 1 ? 'Заказчик' : 'Образовательная организация',
                ],
                'status_comment',
            ]]);
    }


    if ($model->status == 1) {
        $contracts = [
            [
                'label' => 'Посмотреть текст договора',
                'format' => 'raw',
                'value' => Html::a('<span class="glyphicon glyphicon-download-alt"></span>', $model->fullUrl),
            ],
            [
                'attribute' => 'status',
                'value' => $model->statusName($model->status),
            ],
            'start_edu_contract:date',
            'stop_edu_contract:date',
            [
                'attribute' => 'all_funds',
                'value' => round($model->all_funds, 2),
            ],
            [
                'attribute' => 'funds_cert',
                'value' => round($model->funds_cert, 2),
            ],
            [
                'attribute' => 'all_parents_funds',
                'value' => round($model->all_parents_funds),
            ],
            [
                'attribute' => 'rezerv',
                'value' => round($model->rezerv, 2),
            ],
            [
                'attribute' => 'paid',
                'value' => round($model->paid, 2),
            ],
        ];

    }


    if ($model->status == 3) {
        $contracts = [
            [
                'label' => 'Посмотреть текст договора',
                'format' => 'raw',
                'value' => Html::a('<span class="glyphicon glyphicon-download-alt"></span>', $model->fullUrl),
            ],
            [
                'attribute' => 'status',
                'value' => $model->statusName($model->status),
            ],
            'start_edu_contract:date',
            'stop_edu_contract:date',
            'all_funds',
            'funds_cert',
            'all_parents_funds',
            'rezerv',
        ];

    }


    if ($model->status == 0) {
        $contracts = [

            [
                'attribute' => 'status',
                'value' => $model->statusName($model->status),
            ],
            'start_edu_contract:date',
            'stop_edu_contract:date',
            'all_funds',
            'funds_cert',
            'all_parents_funds',
            'rezerv',
        ];

    }

    if ($model->status == 4) {

        $contracts = [
            [
                'label' => 'Посмотреть текст договора',
                'format' => 'raw',
                'value' => Html::a('<span class="glyphicon glyphicon-download-alt"></span>', $model->fullUrl),
            ],
            [
                'attribute' => 'status',
                'value' => $model->statusName($model->status),
            ],
            'start_edu_contract:date',
            'stop_edu_contract:date',
            'date_termnate:date',
        ];
    }

    if ($model->status == 2) {

        $contracts = [
            [
                'label' => 'Посмотреть текст договора',
                'format' => 'raw',
                'value' => Html::a('<span class="glyphicon glyphicon-download-alt"></span>', $model->fullUrl),
            ],
            [
                'attribute' => 'status',
                'value' => $model->statusName($model->status),
            ],
            'start_edu_contract:date',
            'stop_edu_contract:date',
        ];

    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $contracts,
    ]) ?>


    <?php
    if ($model->ocenka == 1 && (
            Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)
            || Yii::$app->user->can(UserIdentity::ROLE_MONITOR)
            || Yii::$app->user->can(UserIdentity::ROLE_ADMINISTRATOR)
            || Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)
        )) {

        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'ocen_fact',
                'ocen_kadr',
                'ocen_mat',
                'ocen_obch',
            ],
        ]);

    }

    if (($model->status === Contracts::STATUS_ACTIVE
            || $model->status === Contracts::STATUS_CLOSED)
        && (Yii::$app->user->can(UserIdentity::ROLE_PAYER)
            || Yii::$app->user->can(UserIdentity::ROLE_OPERATOR))
    ) {
        echo \app\components\widgets\ContractPayDetails\ContractPayDetails::widget(
            [
                'query' => $completenessQuery
            ]
        );
    }
    ?>
    <?php
    if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_CERTIFICATE)) {
        /** @var $certificate Certificates */
        $certificate = Yii::$app->user->identity->certificate;

        echo Html::a('Назад', Url::to(['/personal/certificate-contracts', 'id' => $model->id]), ['class' => 'btn btn-primary']);
        echo '&nbsp;';

        if ($certificate->actual === 1 && $model->status === 1) {
            echo Html::a(!$model->ocenka ? 'Оценить программу' : 'Изменить оценку', Url::to(['/contracts/ocenka', 'id' => $model->id]), ['class' => 'btn btn-success']);
            if ($model->status === Contracts::STATUS_ACTIVE || $model->status === Contracts::STATUS_CLOSED) {
                echo '&nbsp;';
                echo Html::a('Оставить возражение', Url::to(['/disputes/create', 'id' => $model->id]), ['class' => 'btn btn-warning']);
            }
        }
    }
    if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
        echo Html::a('Назад', Url::to(['/personal/organization-contracts', 'id' => $model->id]), ['class' => 'btn btn-primary']);
        if ($model->status === Contracts::STATUS_ACTIVE || $model->status === Contracts::STATUS_CLOSED) {
            echo '&nbsp;';
            echo Html::a('Просмотреть возражения', Url::to(['/disputes/create', 'id' => $model->id]), ['class' => 'btn btn-warning']);
        }
    }
    if (Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)) {
        echo Html::a('Назад', Url::to(['/personal/operator-contracts', 'id' => $model->id]), ['class' => 'btn btn-primary']);
        if ($model->status === Contracts::STATUS_ACTIVE || $model->status === Contracts::STATUS_CLOSED) {
            echo '&nbsp;';
            echo Html::a('Просмотреть возражения', Url::to(['/disputes/create', 'id' => $model->id]), ['class' => 'btn btn-warning']);
        }
    }
    if (Yii::$app->user->can(UserIdentity::ROLE_PAYER)) {
        echo Html::a('Назад', Url::to(['/personal/payer-contracts', 'id' => $model->id]), ['class' => 'btn btn-primary']);
        if ($model->status === Contracts::STATUS_ACTIVE || $model->status === Contracts::STATUS_CLOSED) {
            echo '&nbsp;';
            echo Html::a('Просмотреть возражения', Url::to(['/disputes/create', 'id' => $model->id]), ['class' => 'btn btn-warning']);
        }
    }
    ?>
    <?php
    if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE) && $model->canBeTerminated && $certificate->actual > 0) {
        if (date("m") == 12) {
            $month = 1;
            $year = date("Y") + 1;
        } else {
            $month = date("m") + 1;
            $year = date("Y");
        }
        echo '<div class="pull-right">';
        echo ModalCheckLink::widget([
            'link' => Html::a('Расторгнуть договор', Url::to(['/contracts/terminate', 'id' => $model->id]), ['class' => 'btn btn-danger']),
            'buttonOptions' => ['label' => 'Расторгнуть договор', 'class' => 'btn btn-danger'],
            'content' => 'Вы собираетесь расторгнуть выбранный договор. Расторжение договора осуществляется с первого дня месяца, следующего за месяцем направления уведомления о расторжении договора. 
                    В случае если Вы подтвердите расторжение договора будет запущена процедура расторжения договора, которая не имеет обратной силы. Средства сертификата, зарезервированные на оплату договора и не использованные на данный момент, вернутся на баланс Вашего сертификата первого числа следующего месяца.
                    Вы действительно хотите расторгнуть данный договор с 1.' . $month . '.' . $year . '?',
            'title' => 'Расторгнуть договор?',
            'label' => 'Да, я уверен, что хочу Расторгнуть договор.',
        ]);
        echo '</div>';

    }

    if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)
        && ($model->status === Contracts::STATUS_REQUESTED || $model->status === Contracts::STATUS_ACCEPTED)
        && $certificate->actual
    ) {

        if (date("m") == 12) {
            $month = 1;
            $year = date("Y") + 1;
        } else {
            $month = date("m") + 1;
            $year = date("Y");
        }
        echo '<div class="pull-right">';
        echo ModalCheckLink::widget([
            'link' => Html::a('Отменить заявку', Url::to(['/contracts/termrequest', 'id' => $model->id]), ['class' => 'btn btn-danger']),
            'buttonOptions' => ['label' => 'Отменить заявку', 'class' => 'btn btn-danger'],
            'content' => 'Отклоняя заявку Вы отказываетесь от заключения договора на обучение по выбранной программе. Деньги, зарезервированные на оплату договора вернутся на сертификат в полном объеме, но передумывать отклонять заявку будет уже поздно. Вы уверены, что хотите отклонить заявку?',
            'title' => 'Отменить заявку?',
            'label' => 'Да, я уверен, что хочу Отменить заявку.',
        ]);
        echo '</div>';
    }

    if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION) && $model->canBeTerminated) {
        if (date("m") == 12) {
            $month = 1;
            $year = date("Y") + 1;
        } else {
            $month = date("m") + 1;
            $year = date("Y");
        }
        echo '<div class="pull-right">';
        echo ModalCheckLink::widget([
            'link' => Html::a('Расторгнуть договор', Url::to(['/contracts/terminate', 'id' => $model->id]), ['class' => 'btn btn-danger']),
            'buttonOptions' => ['label' => 'Расторгнуть договор', 'class' => 'btn btn-danger'],
            'content' => 'Вы собираетесь расторгнуть выбранный договор. Расторжение договора осуществляется с первого дня месяца, следующего за месяцем направления уведомления о расторжении договора. 
В случае если Вы подтвердите расторжение договора будет запущена процедура расторжения договора, которая не имеет обратной силы. Средства сертификата, зарезервированные на оплату договора и не использованные на данный момент, вернутся на баланс сертификата первого числа следующего месяца.
Вы действительно хотите расторгнуть данный договор с 1.' . $month . '.' . $year . '?',
            'title' => 'Расторгнуть договор?',
            'label' => 'Да, я уверен, что хочу расторгнуть договор.',
        ]);
        echo '</div>';
    } elseif ((!$model->canBeTerminated && $model->status === Contracts::STATUS_ACTIVE && $model->wait_termnate < 1)
        && (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION))) {
        echo '<div class="pull-right text-warning">Договор не может быть расторгнут до начала действия</div>';
    }

    if (Yii::$app->user->can($model->terminatorUserRole) && ($model->status === Contracts::STATUS_CLOSED || $model->wait_termnate > 0)) {
        echo '<br /><br /><div class="alert alert-warning">Для юридического закрепления расторжения договора заполните <a href="' . Url::to(['application-close-pdf', 'id' => $model->id]) . '">бланк уведомления о расторжении договора</a> и передайте заявление ' . (Yii::$app->user->can('certificate') ? 'Поставщику' : 'Заказчику') . ' услуг</div>';
    }
    ?>

    <br/><br/>
    <p style="font-size: xx-small;">Если Вам необходимо снова распечатать заявление &ndash; <a
                href="<?= Url::to(['application-pdf', 'id' => $model->id]) ?>">жмите сюда</a></p>

</div>
