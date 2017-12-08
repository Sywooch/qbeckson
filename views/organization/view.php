<?php

use app\models\Cooperate;
use app\models\forms\ConfirmRequestForm;
use app\models\forms\CooperateForFuturePeriodForm;
use app\models\forms\CooperateForFuturePeriodTypeForm;
use app\models\OperatorSettings;
use app\models\Payers;
use app\models\UserIdentity;
use kartik\datecontrol\DateControl;
use trntv\filekit\widget\Upload;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Organization */
/* @var $confirmRequestForm ConfirmRequestForm */
/* @var $cooperateForFuturePeriodForm CooperateForFuturePeriodForm */
/* @var $operatorSettings OperatorSettings */
/* @var $futurePeriodCooperate Cooperate */
/* @var $cooperateForFuturePeriodTypeForm CooperateForFuturePeriodTypeForm */

$this->title = $model->name;

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if (isset($roles['operators'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['/personal/operator-organizations']];
}
if (isset($roles['organizations'])) {
    $this->params['breadcrumbs'][] = 'Организации';
}
if (isset($roles['payer'])) {
    $cooperation = $model->getCooperation();
    if ($cooperation && !empty($cooperation->total_payment_limit)) {
        $commitments = \app\models\Contracts::getCommitments($cooperation->id);
        $summary = \app\models\Invoices::getSummary($cooperation->id);
        $commitmentsNextMonth = \app\models\Contracts::getCommitmentsNextMonth($cooperation->id);

        if ($summary + $commitmentsNextMonth > $cooperation->total_payment_limit) {
            Yii::$app->session->setFlash('info', 'В связи с ожидаемым достижением максимальной суммы по заключенному договору рекомендуется заключить дополнительное соглашение к договору, установив максимальную сумму не менее &ndash; ' . round($commitments, 2) . ' рублей');
        } elseif (!empty($commitments) && $cooperation->total_payment_limit > $commitments) {
            Yii::$app->session->setFlash('warning', 'Вы можете уменьшить максимальную сумму по договору до &ndash; ' . round($commitments, 2) . ' рублей');
        }
    }
    if ($futurePeriodCooperate && !empty($futurePeriodCooperate->total_payment_limit)) {
        $futurePeriodCooperateCommitments = \app\models\Contracts::getCommitments($futurePeriodCooperate->id);
        $futurePeriodCooperateSummary = \app\models\Invoices::getSummary($futurePeriodCooperate->id);
    }
}
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
$('#type').change(function() {
    var val = $(this).val();
    $('.item').hide().children('.text').hide();
    $('.' + val).show().children('.' + val + 'Text').show();
})
$('#iscustomvalue').change(function() {
    if(this.checked) {
        $('.extend').show();
        return;
    }
    $('.extend').hide();
});

$('#cooperateforfutureperiodtypeform-type').change(function() {
    var val = $(this).val();
    $('.item').hide().children('.future-cooperate-type-text').hide();
    $('.future-cooperate-type-' + val).show().children('.' + val + 'FutureCooperateTypeText').show();
});
$('.future-cooperate-type-change').change(function() {    
    var val = $(this).val();

    if (val == 'extend') {
        $('.maximum-amount').show();
    } else {
        $('.maximum-amount').hide();
    }

    $('.item').hide().children('.future-cooperate-text').hide();
    $('.future-cooperate-' + val).show().children('.' + val + 'FutureCooperateText').show();
});

$('.future_period_checkbox').on('click', function() {
    $('#choose-next-year-cooperate-type-modal').modal();
    $('.use-new-cooperate-type').prop('checked', false);
    $('.use-current-cooperate-type').prop('checked', false);
    $('.add-cooperate-confirm').hide();
    $('.cooperate-with-new-type').hide();
    $('.maximum-amount').hide();
});

$('.use-current-cooperate-type').on('click', function() {
    $('.add-cooperate-confirm').find('input').prop('checked', false);
    $('.add-cooperate-button').hide();
    
    $('.cooperate-with-old-type').show();
    
    $(this).prop('checked', true);
    $('.use-new-cooperate-type').prop('checked', false);

    $('.cooperate-with-new-type').hide();

    if ($('.use-current-cooperate-type').data('cooperate-with-maximum-amount') == 1) {
        $('.maximum-amount').show();
    } else {
        $('.maximum-amount').hide();
    }
});

$('.use-new-cooperate-type').on('click', function() {  
    $('.add-cooperate-confirm').find('input').prop('checked', false);
    $('.add-cooperate-button').hide();
    
    $('.cooperate-with-new-type').show();
    
    $('.cooperate-with-old-type').hide();    
    
    $(this).prop('checked', true);
    $('.use-current-cooperate-type').prop('checked', false);
    
    var val = $('.future-cooperate-type-change').val();
    
    if (val == 'extend') {
        $('.maximum-amount').show();
    } else {
        $('.maximum-amount').hide();
    }
    
    $('.item').hide().children('.future-cooperate-text').hide();
    $('.future-cooperate-' + val).show().children('.' + val + 'FutureCooperateText').show();
});

$('.save-requisites-button').on('click', function() {    
    var number = $('#cooperateforfutureperiodform-futurecooperatenumber'),
        date = $('#cooperateforfutureperiodform-futurecooperatedate-disp'),
        cooperateRequisites = $('.cooperate-requisites');
    
    $("#set-requisites-modal").modal("hide");
    if (number.val() != '' && date.val() != '') {
        cooperateRequisites.find('span').html('Договор от ' + date.val() + ' ' + number.val() + ' ');
        cooperateRequisites.find('a').html('изменить');
    } else {
        cooperateRequisites.find('span').html('');
        cooperateRequisites.find('a').html('указать');
    }
});

$('#choose-next-year-cooperate-type-form').on('change', function() {
    futureCooperateFormValidate();
});

$('#choose-next-year-cooperate-type-form').on('input', function() {
    futureCooperateFormValidate();
});

function futureCooperateFormValidate() {
    var form = $('#choose-next-year-cooperate-type-form');
    
    $.ajax({
        method: 'POST',
        data: form.serialize(),
        success: function(data) {
            if ($.isEmptyObject(data)) {
                $('.add-cooperate-confirm').show();
            } else {
                $('.add-cooperate-confirm').hide();
            }
        }
    })
}

$('.modal-dialog').on('click', function(e) {
    e.stopPropagation();
});

$('.modal').on('click', function() {
    $('.future_period_checkbox').prop('checked', false);
    $('.modal').modal('hide');
});

$('.close').on('click', function() {
    var modal = $(this).parents('.modal').first();
    
    if ('choose-next-year-cooperate-type-modal' == modal.attr('id')) {
        $('.future_period_checkbox').prop('checked', false);
    }
    
    modal.modal('hide');    
});
JS;
$this->registerJs($js, $this::POS_READY);

?>
<div class="organization-view col-md-8 col-md-offset-2">

    <?php
    if ($model->raiting) {
        echo '<h1 class="pull-right">' . $model->raiting . '%</h1>';
    } else {
        echo '<h3 class="pull-right">Рейтинга нет</h3>';
    }
    ?>

    <h3><?= Html::encode($this->title) ?></h3>

    <?php
    if (isset($roles['payer'])) {
        $payers = new Payers();
        $payer = $payers->getPayer();

        $cooperates = (new \yii\db\Query())
            ->select(['id'])
            ->from('cooperate')
            ->where(['organization_id' => $model->id])
            ->andWhere(['status' => [0, 1]])
            ->andWhere(['payer_id' => $payer])
            ->one();

        if ($cooperates['id']) {

            $cooperate = Cooperate::findOne($cooperates['id']);
            $cooperatedate = explode('-', $cooperate->date);

            if (!empty($cooperate->date)) {
                echo DetailView::widget([
                    'model' => $cooperate,
                    'attributes' => [
                        [
                            'label' => 'Соглашение',
                            'value' => 'от ' . $cooperatedate[2] . '.' . $cooperatedate[1] . '.' . $cooperatedate[0] . ' № ' . $cooperate->number,
                        ],
                    ],
                ]);
            }
        }
    }

    $license_date = explode('-', $model->license_date);
    ?>

    <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'full_name',
                [
                    'attribute'=>'type',
                    'value' => $model::types()[$model->type],
                ],
                'address_actual',
                [
                    'attribute' => 'mun',
                    'label' => 'Основной район (округ)',
                    'value' => function ($model) {
                        /** @var \app\models\Organization $model */
                        if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
                            return $model->municipality->name;
                        } else {
                            return Html::a(
                                $model->municipality->name,
                                ['mun/view', 'id' => $model->municipality->id],
                                ['target' => '_blank', 'data-pjax' => '0']
                            );
                        }
                    },
                    'format' => 'raw',
                ],
                'phone',
                [
                    'attribute' => 'email',
                    'format' => 'email',
                ],
                [
                    'attribute' => 'site',
                    'format' => 'url',
                ],
                'fio_contact',
                [
                  'label' => 'Лицензия',
                    'value' => 'Лицензия от '.$license_date[2].'.'.$license_date[1].'.'.$license_date[0].' №'.$model->license_number.' выдана '.$model->license_issued.'.',
                ],
                [
                    'attribute'=>'actual',
                    'value'=>$model->actual == 1 ? 'Осуществляет деятельность в рамках системы' : 'Деятельность в рамках системы приостановлена',
            ],
        ],
    ])
    ?>
    
    <?php
    if (isset($roles['operators'])) {

        echo DetailView::widget([
            'model'      => $model,
            'attributes' => [
                'inn',
                'KPP',
                'OGRN',
                'okopo',
                'address_legal',
                'last_year_contract',
                'cratedate:date',
            ],
        ]);

        $requisiteAttributes = [
            'bank_name',
            'bank_bik',
            'bank_sity',
            'korr_invoice',
            'correspondent_invoice',
            'rass_invoice',
        ];

        // не отображать корреспондентский счет если он не указан
        if (!$model->correspondent_invoice) {
            $index = array_search('correspondent_invoice', $requisiteAttributes);
            unset($requisiteAttributes[$index]);
        }

        echo DetailView::widget([
            'model'      => $model,
            'attributes' => $requisiteAttributes,
        ]);
    }
    ?>

    <?php
    $cooperate = (new \yii\db\Query())
        ->select(['id'])
        ->from('cooperate')
        ->where(['organization_id' => $model->id])
        ->andWhere(['status' => 1])
        ->count();

    $model['cooperate'] = $cooperate;

    if (isset($roles['payer'])) {
        $payers = new Payers();
        $payer = $payers->getPayer();

        $amount = $model->getChildrenCount($payer);
        $contracts = $model->getContractsCount($payer);

    } else {
        $amount = $model->getChildrenCount();
        $contracts = $model->getContractsCount();
    }
    ?>

    <?php
    if (isset($roles['certificate'])) {
        echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'max_child',
            [
                'label' => 'Число обучающихся',
                'value' => $amount,
            ],
            [
                'label' => Html::a(
                    'Сертифицированных программ',
                    Url::to(['/personal/certificate-programs', 'organization_id' => $model->id]),
                    ['class' => 'blue', 'target' => '_blank']
                ),
                'value'=>$model->getCertprogram(),
            ],
        ],
    ]);
        }
        if (isset($roles['payer'])) {
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'max_child',
                    [
                        'label' => 'Число обучающихся',
                        'value' => $amount,
                    ],
                    [
                        'value' => $contracts,
                        'label'=> Html::a(
                            'Число договоров',
                            Url::to([
                                'personal/payer-contracts',
                                'SearchActiveContracts[organizationName]' => $model->name,
                                'SearchActiveContracts[organization_id]' => $model->id,
                            ]),
                            ['class' => 'blue', 'target' => '_blank']
                        ),
                    ],
                    [
                        'value' => $model->getCertprogram(),
                        'label' => Html::a(
                            'Сертифицированных программ',
                            Url::to([
                                'personal/payer-programs',
                                'SearchPrograms[organization]' => $model->name,
                                'SearchPrograms[organization_id]' => $model->id
                            ]),
                            ['class' => 'blue', 'target' => '_blank']
                        ),
                    ],
                ],
            ]);
        }
    
        if (isset($roles['operators'])) {
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'max_child',
                    [
                        'label' => 'Число обучающихся',
                        'value' => $amount,
                    ],
                    [
                        'value' => $contracts,
                        'label'=> Html::a(
                            'Число договоров',
                            [
                                'personal/operator-contracts',
                                'SearchActiveContracts[organizationName]' => $model->name,
                                'SearchActiveContracts[organization_id]' => $model->id
                            ],
                            ['class' => 'blue', 'target' => '_blank']
                        ),
                    ],
                    [
                        'value' => $model->getCertprogram(),
                        'label' => Html::a(
                            'Сертифицированных программ',
                            [
                                'personal/operator-programs',
                                'SearchOpenPrograms[organization]' => $model->name,
                                'SearchOpenPrograms[organization_id]' => $model->id,
                            ],
                            ['class' => 'blue', 'target' => '_blank']
                        ),
                    ],
                ],
            ]);
        }
        ?>
    
    <?php
    if (isset($roles['payer'])) {

        $payers = new Payers();
        $payer = $payers->getPayer();

        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'label' => Html::a(
                        'Выставлено счетов и авансов',
                        [
                            '/personal/payer-invoices',
                            'InvoicesSearch[organization]' => $model->name,
                            'InvoicesSearch[organization_id]' => $model->id
                        ],
                        ['class' => 'blue', 'target' => '_blank']
                    ),
                    'value' => $model->invoiceCount($model->id, $payer->id),
                ],
            ],
        ]);
    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'about:ntext',
        ],
    ]) ?>

    <div>
        <?php
        if (isset($roles['operators']) && $model->status !== \app\models\Organization::STATUS_REFUSED) {
            if ($model->actual === 0) {
                echo '<div class="pull-right">';
                echo Html::a('Разрешить деятельность', Url::to(['/organization/actual', 'id' => $model->id]), ['class' => 'btn btn-success']);
                echo '</div>';
            } else {
                $previus = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['organization_id' => $model->id])
                    ->andWhere(['status' => 1])
                    ->count();
                if (!$previus) {
                    echo '<div class="pull-right">';
                    echo Html::a('Приостановить', Url::to(['/organization/noactual', 'id' => $model->id]), ['class' => 'btn btn-danger  text-right']);
                    echo '</div>';
                }
            }

            echo Html::a('Пересчитать лимит', Url::to(['/organization/newlimit', 'id' => $model->id]), ['class' => 'btn btn-primary']);
            echo '&nbsp;';
            echo Html::a('Пересчитать рейтинг', Url::to(['/organization/raiting', 'id' => $model->id]), ['class' => 'btn btn-primary']);

            echo '<br><br>';
            echo Html::a('Назад', '/personal/operator-organizations', ['class' => 'btn btn-primary']);
            echo '&nbsp;';
            echo Html::a('Редактировать', Url::to(['/organization/update', 'id' => $model->id]), ['class' => 'btn btn-primary']);

            $previus = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['organization_id' => $model->id])
                ->count();
            if (!$previus) {
                echo '&nbsp;';
                echo Html::a('Удалить', Url::to(['/organization/delete', 'id' => $model->id]), ['class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post']]);
            }
        } elseif (isset($roles['operators'])) {
            ?>
            <div class="well">
                <?php if (!empty($model->license)): ?>
                    <?= Html::a('Лицензия (документ)', $model->license[0]->getUrl()) ?>
                    <br>
                <? endif; ?>
                <?php if (!empty($model->charter)): ?>
                    <?= Html::a('Устав (документ)', $model->charter[0]->getUrl()) ?>
                    <br>
                <? endif; ?>
                <?php if (!empty($model->statement)): ?>
                    <?= Html::a('Выписка из ЕГРЮЛ/ЕГРИП (документ)', $model->statement[0]->getUrl()) ?>
                <? endif; ?>
                <?php if (!empty($model->documents)): ?>
                    <h4>Иные документы:</h4>
                    <?php foreach ($model->documents as $i => $document): ?>
                        <?= Html::a('Документ ' . ($i + 1), $document->getUrl()) ?><br/>
                    <? endforeach; ?>
                <? endif; ?>
            </div>
            <?php
            echo '<br><br>';
            echo Html::a('Назад', '/personal/operator-organizations', ['class' => 'btn btn-primary']);
            echo '&nbsp;';
        }

        if (isset($roles['organizations'])) {
            echo Html::a('Назад', '/personal/organization-info', ['class' => 'btn btn-primary']);
        }

        if (isset($roles['payer'])) {
            echo Html::a('Назад', ['personal/payer-organizations'], ['class' => 'btn btn-primary']);

            if (!empty($cooperation)) {
                if (count($cooperation->contracts) < 1 && $cooperation->status === Cooperate::STATUS_ACTIVE) {
                    echo $this->render(
                        '../cooperate/reject-contract',
                        ['cooperation' => $cooperation]
                    );
                }

                $documentLabel = $cooperation->total_payment_limit ? 'Договор c суммой' : 'Договор без суммы';
                $alternativeDocumentLabel = !$cooperation->total_payment_limit ? 'Договор c суммой' : 'Договор без суммы';

                $activeDocumentLink = null !== $cooperation->getActiveDocumentUrl() ? Html::a($documentLabel, [$cooperation->getActiveDocumentUrl()], ['target' => 'blank']) : '';
                $alternativeDocumentLink = null !== $cooperation->getAlternativeDocumentUrl() ? Html::a($alternativeDocumentLabel, [$cooperation->getAlternativeDocumentUrl()], ['target' => 'blank']) : '';

                if ($activeDocumentLink) { ?>

                    <hr>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?= Cooperate::documentNames()[$operatorSettings->document_name] ?>, используемый для оплаты услуг на текущий момент:
                        </div>

                        <table class="table table-striped table-bordered detail-view">
                            <tbody>
                            <tr>
                                <th>Тип соглашения</th>
                                <td><?= $activeDocumentLink ?> <a href="javascript:void(0);" onclick="$('#change-cooperate-type-modal').modal()">(изменить)</a>
                                    <?php Modal::begin([
                                        'id' => 'change-cooperate-type-modal',
                                        'header' => 'Изменить тип соглашения',
                                    ]);

                                    $form = ActiveForm::begin([
                                        'id' => 'cooperate-type-change-form',
                                        'enableAjaxValidation' => true,
                                        'enableClientValidation' => false,
                                    ]); ?>

                                    <p>
                                        Вы уверены, что хотите поменять соглашение? Не забудьте при необходимости изменить реквизиты,
                                        но имейте ввиду, что если у Ваших детей уже есть договоры с данной организацией,
                                        то в них фигурируют прошлые реквизиты
                                    </p>

                                    <?= $form->field($confirmRequestForm, 'type')->dropDownList(Cooperate::documentTypes()) ?>
                                    <div class="item <?= Cooperate::DOCUMENT_TYPE_GENERAL ?>">
                                        <p class="text <?= Cooperate::DOCUMENT_TYPE_GENERAL ?>Text">
                                            <small>* Рекомендуется заключать в случае если для расходования средств не требуется
                                                постановки расходного обязательства в казначействе (подходит для СОНКО)
                                            </small>
                                            <br>
                                            <?= Html::a('Просмотр договора', $operatorSettings->getGeneralDocumentUrl()); ?>
                                        </p>
                                    </div>
                                    <div class="item <?= Cooperate::DOCUMENT_TYPE_CUSTOM ?>" style="display: "<?= Cooperate::DOCUMENT_TYPE_CUSTOM == $cooperation->document_type ? 'block' : 'none' ?>>
                                        <p class="text <?= Cooperate::DOCUMENT_TYPE_CUSTOM ?>Text" style="display: none;">
                                            <small>* Вы можете сделать свой вариант договора (например, проставить заранее реквизиты в
                                                предлагаемый оператором), но не уходите от принципов ПФ. Если Вы выберите данный
                                                вариант,
                                                укажите, пожалуйста, сделан ли Ваш договор с указанием максимальной суммы (в этом случае
                                                укажите сумму), или без нее, чтобы система отслеживала необходимость заключения
                                                допсоглашений
                                                и информировала Вас об этом при необходимости.
                                            </small>
                                        </p>

                                        <?= $form->field($confirmRequestForm, 'isCustomValue')->checkbox(); ?>
                                        <?= $form->field($confirmRequestForm, 'document')->widget(Upload::class, [
                                            'url' => ['file-storage/upload'],
                                            'maxFileSize' => 10 * 1024 * 1024,
                                            'acceptFileTypes' => new JsExpression('/(\.|\/)(doc|docx)$/i'),
                                        ]); ?>
                                    </div>
                                    <div class="item <?= Cooperate::DOCUMENT_TYPE_EXTEND ?>" style="display: <?= Cooperate::DOCUMENT_TYPE_EXTEND == $cooperation->document_type ? 'block' : 'none' ?>">
                                        <p class="text <?= Cooperate::DOCUMENT_TYPE_EXTEND ?>Text" style="display: none;">
                                            <small>* Рекомендуется заключать в случае если для постановки расходного обязательства на
                                                исполнение необходимо зафиксировать сумму договора (подходит для АУ). Использование
                                                данного
                                                договора предполагает необходимость регулярного заключения дополнительных соглашений
                                                (информационная система будет давать подсказки)
                                            </small>
                                            <br>
                                            <?= Html::a('Просмотр договора', $operatorSettings->getExtendDocumentUrl()); ?>
                                        </p>
                                        <?= $form->field($confirmRequestForm, 'value')->textInput() ?>
                                    </div>
                                    <div class="form-group clearfix">
                                        <?= Html::submitButton('Изменить тип соглашения', ['class' => 'btn btn-success']) ?>
                                    </div>

                                    <? $form->end();

                                    Modal::end(); ?>
                                </td>
                            </tr>

                            <?php if ($cooperation->status === Cooperate::STATUS_NEW) {
                                echo ' ';
                                echo $this->render(
                                    '../cooperate/reject-request',
                                    ['model' => $cooperation]
                                );
                                echo $this->render(
                                    '../cooperate/confirm-request',
                                    ['cooperation' => $cooperation]
                                );
                            }
                            if ($cooperation->status === Cooperate::STATUS_CONFIRMED &&
                                null === $cooperation->number &&
                                null === $cooperation->date
                            ) {
                                echo $this->render(
                                    '../cooperate/requisites',
                                    [
                                        'model' => $cooperation,
                                        'toggleButtonClass' => null,
                                        'label' => 'Сведения о реквизитах соглашения/договора не внесены',
                                    ]
                                );
                                echo '<br /><br />';
                            } else if (($cooperation->status === Cooperate::STATUS_CONFIRMED &&
                                    null !== $cooperation->number &&
                                    null !== $cooperation->date) ||
                                $cooperation->status === Cooperate::STATUS_ACTIVE
                            ) { ?>
                            <tr>
                                <th>Реквизиты соглашения</th>
                                <td>договор от <?= \Yii::$app->formatter->asDate($cooperation->date) ?> №<?= $cooperation->number ?>
                                    <?= $this->render(
                                        '../cooperate/requisites',
                                        [
                                            'model' => $cooperation,
                                            'toggleButtonClass' => '',
                                            'label' => '(изменить)',
                                        ]
                                    ); ?>
                                </td>
                            </tr>
                                <?php if ($cooperation->status == Cooperate::STATUS_ACTIVE && $cooperation->document_type == Cooperate::DOCUMENT_TYPE_EXTEND && Yii::$app->user->can('payers')) { ?>
                            <tr>
                                <th>Установлена максимальная сумма по договору, рублей</th>
                                <td><?= round($cooperation->total_payment_limit, 2) ?>
                                    <?= $this->render(
                                        '../cooperate/payment-limit',
                                        [
                                            'toggleButtonClass' => '',
                                            'label' => '(изменить)',
                                            'model' => $cooperation,
                                        ]
                                    ); ?>
                                </td>
                            </tr>
                                <?php }
                            }
                            if ($cooperation->status === Cooperate::STATUS_CONFIRMED &&
                                null !== $cooperation->number &&
                                null !== $cooperation->date
                            ) {
                                echo ' ';
                                echo Html::a(
                                    'Одобрить',
                                    ['cooperate/confirm-contract', 'id' => $cooperation->id],
                                    ['class' => 'btn btn-primary']
                                );
                            } ?>

                            <?php if (!empty($commitments)): ?>
                                <tr>
                                    <th>Совокупная сумма подтвержденных обязательств по договору</th>
                                    <td>
                                        <?= round($commitments, 2) . ' рублей'; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if (!empty($summary)): ?>
                                <tr>
                                    <th>Совокупная сумма оплаченных ранее счетов</th>
                                    <td>
                                        <?= round($summary, 2) . ' рублей'; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                <?php } ?>
                <br>

                <div class="checkbox-container">
                    <?= Html::checkbox('', !$futurePeriodCooperate ? false : true, [
                        'label' => 'С поставщиком услуг заключен ' . Cooperate::documentNames()[$operatorSettings->document_name] . ', для оплаты услуг в будущем периоде (с ' . \Yii::$app->formatter->asDate($operatorSettings->future_program_date_from) . ' по ' . \Yii::$app->formatter->asDate($operatorSettings->future_program_date_to) . ').',
                        'class' => 'future_period_checkbox',
                        'disabled' => $futurePeriodCooperate ? true : false,
                    ]) ?>
                </div>

                <br>

                <?php Modal::begin([
                    'id' => 'choose-next-year-cooperate-type-modal',
                    'header' => 'Выберите тип соглашения действующем в будущем периоде (с ' . \Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_from) . ' по ' . \Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_to) . ')',
                ]); ?>
                <?php $futurePeriodForm = ActiveForm::begin([
                    'id' => 'choose-next-year-cooperate-type-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                ]); ?>

                <?= $futurePeriodForm->field($cooperateForFuturePeriodForm, 'useCurrentCooperateType')->checkbox(['data' => ['cooperate-with-maximum-amount' => $cooperation->document_type == 'extend' ? 1 : 0], 'class' => 'use-current-cooperate-type']) ?>
                <?= Html::checkbox('', false, [
                'class' => 'use-new-cooperate-type',
                'label' => 'Указать сведения о соглашении, которое будет использоваться для оплаты по договорам в будущем периоде',
            ]) ?>

                <hr>
                <br>

                <div class="cooperate-with-new-type" style="display: none;">
                    <p class="cooperate-requisites" >
                        Реквизиты соглашения: <span></span><a href="javascript:void(0); $('#set-requisites-modal').modal();">указать</a>
                    </p>

                    <?php Modal::begin([
                        'id' => 'set-requisites-modal',
                        'header' => '<h2>Реквизиты соглашения</h2>',
                    ]);
                    ?>
                    <?= $futurePeriodForm->field($cooperateForFuturePeriodForm, 'futureCooperateNumber')->textInput(['maxlength' => true]) ?>
                    <?= $futurePeriodForm->field($cooperateForFuturePeriodForm, 'futureCooperateDate')->widget(DateControl::class, [
                        'type' => DateControl::FORMAT_DATE,
                        'ajaxConversion' => false,
                        'options' => [
                            'pluginOptions' => [
                                'autoclose' => true
                            ]
                        ]
                    ]) ?>
                    <div class="form-group clearfix">
                        <?= Html::button('Сохранить', ['class' => 'btn btn-success save-requisites-button']) ?>
                    </div>
                    <?php Modal::end(); ?>

                    <?= $futurePeriodForm->field($cooperateForFuturePeriodForm, 'type')->dropDownList([Cooperate::DOCUMENT_TYPE_GENERAL => Cooperate::documentTypes()[Cooperate::DOCUMENT_TYPE_GENERAL], Cooperate::DOCUMENT_TYPE_EXTEND => Cooperate::documentTypes()[Cooperate::DOCUMENT_TYPE_EXTEND]], ['class' => ['form-control future-cooperate-type-change']]); ?>
                    <div class="item future-cooperate-<?= Cooperate::DOCUMENT_TYPE_GENERAL ?>">
                        <p class="future-cooperate-text <?= Cooperate::DOCUMENT_TYPE_GENERAL ?>FutureCooperateText">
                            <small>* Рекомендуется заключать в случае если для расходования средств не требуется постановки расходного обязательства в казначействе (подходит для СОНКО)
                            </small>
                            <br>
                            <?= Html::a('Просмотр договора', $operatorSettings->getGeneralDocumentUrl()); ?>
                        </p>
                    </div>
                    <div class="item future-cooperate-<?= Cooperate::DOCUMENT_TYPE_EXTEND ?>" style="display: none">
                        <p class="future-cooperate-text <?= Cooperate::DOCUMENT_TYPE_EXTEND ?>FutureCooperateText" style="display: none;">
                            <small>* Рекомендуется заключать в случае если для постановки расходного обязательства на исполнение необходимо зафиксировать сумму договора (подходит для АУ).
                                Использование данного
                                договора предполагает необходимость регулярного заключения дополнительных соглашений (информационная система будет давать подсказки)
                            </small>
                            <br>
                            <?= Html::a('Просмотр договора', $operatorSettings->getExtendDocumentUrl()); ?>
                        </p>
                    </div>
                </div>

                <div class="cooperate-with-old-type" style="display: none;">
                    <p>Реквизиты соглашения: договор от <?= \Yii::$app->formatter->asDate($cooperation->date) . ' №' . $cooperation->number ?></p>
                </div>

                <div class="maximum-amount" style="display: none;">
                    <?= $futurePeriodForm->field($cooperateForFuturePeriodForm, 'maximumAmount')->textInput() ?>
                </div>

                <div class="checkbox-container add-cooperate-confirm" style="display: none">
                    <?= Html::checkbox('', false, [
                        'label' => 'Уверены, что необходимая информация о соглашении введена правильно',
                        'onClick' => 'showNextContainer(this);'
                    ]) ?>
                </div>

                <div class="form-group clearfix center add-cooperate-button" style="display: none;">
                    <?= Html::submitButton('добавить соглашение', ['class' => 'btn btn-success']) ?>
                </div>

                <?php $futurePeriodForm->end(); ?>
                <?php Modal::end(); ?>

                <?php if ($futurePeriodCooperate): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= Cooperate::documentNames()[$operatorSettings->document_name] ?>, использование которого предусматривается в будущем периоде:
                    </div>

                    <table class="table table-striped table-bordered detail-view">
                        <tbody>
                        <tr>
                            <th>Тип соглашения</th>
                            <td><?= Cooperate::documentTypes()[$futurePeriodCooperate->document_type] ?>
                                <?php Modal::begin([
                                    'header' => 'Изменить тип соглашения будущего периода',
                                    'id' => 'future-period-cooperate-type-modal',
                                    'toggleButton' => [
                                        'tag' => 'a',
                                        'label' => '(изменить)',
                                    ],
                                ]) ?>
                                <?php $futurePeriodCooperateTypeForm = ActiveForm::begin([
                                    'enableAjaxValidation' => true,
                                    'enableClientValidation' => false,
                                ]) ?>

                                <?= $futurePeriodCooperateTypeForm->field($cooperateForFuturePeriodTypeForm, 'type')->dropDownList([Cooperate::DOCUMENT_TYPE_GENERAL => Cooperate::documentTypes()[Cooperate::DOCUMENT_TYPE_GENERAL], Cooperate::DOCUMENT_TYPE_EXTEND => Cooperate::documentTypes()[Cooperate::DOCUMENT_TYPE_EXTEND]], ['class' => ['form-control']]); ?>
                                <div class="item future-cooperate-type-<?= Cooperate::DOCUMENT_TYPE_GENERAL ?>">
                                    <p class="future-cooperate-type-text <?= Cooperate::DOCUMENT_TYPE_GENERAL ?>FutureCooperateTypeText">
                                        <small>* Рекомендуется заключать в случае если для расходования средств не требуется постановки расходного обязательства в казначействе (подходит для СОНКО)
                                        </small>
                                        <br>
                                        <?= Html::a('Просмотр договора', $operatorSettings->getGeneralDocumentUrl()); ?>
                                    </p>
                                </div>
                                <div class="item future-cooperate-type-<?= Cooperate::DOCUMENT_TYPE_EXTEND ?>" style="display: <?= Cooperate::DOCUMENT_TYPE_EXTEND == $futurePeriodCooperate->document_type ? 'block' : 'none' ?>">
                                    <p class="future-cooperate-type-text <?= Cooperate::DOCUMENT_TYPE_EXTEND ?>FutureCooperateTypeText" style="display: none;">
                                        <small>* Рекомендуется заключать в случае если для постановки расходного обязательства на исполнение необходимо зафиксировать сумму договора (подходит для АУ).
                                            Использование данного
                                            договора предполагает необходимость регулярного заключения дополнительных соглашений (информационная система будет давать подсказки)
                                        </small>
                                        <br>
                                        <?= Html::a('Просмотр договора', $operatorSettings->getExtendDocumentUrl()); ?>
                                    </p>
                                    <?= $futurePeriodCooperateTypeForm->field($cooperateForFuturePeriodTypeForm, 'maximumAmount')->textInput() ?>
                                </div>

                                <?= Html::submitButton('изменить', ['class' => 'btn btn-primary']) ?>

                                <?php $futurePeriodCooperateTypeForm->end() ?>
                                <?php Modal::end() ?>
                            </td>
                        </tr>

                        <?php if (($futurePeriodCooperate->status === Cooperate::STATUS_CONFIRMED &&
                                null !== $futurePeriodCooperate->number &&
                                null !== $futurePeriodCooperate->date) ||
                            $futurePeriodCooperate->status === Cooperate::STATUS_ACTIVE
                        ) { ?>
                            <tr>
                                <th>Реквизиты соглашения</th>
                                <td>договор от <?= \Yii::$app->formatter->asDate($futurePeriodCooperate->date) ?> №<?= $futurePeriodCooperate->number ?>
                                    <?= $this->render(
                                        '../cooperate/requisites',
                                        [
                                            'model' => $futurePeriodCooperate,
                                            'toggleButtonClass' => '',
                                            'label' => '(изменить)',
                                        ]
                                    ); ?>
                                </td>
                            </tr>
                            <?php if ($futurePeriodCooperate->status == Cooperate::STATUS_ACTIVE && $futurePeriodCooperate->document_type == Cooperate::DOCUMENT_TYPE_EXTEND && Yii::$app->user->can('payers')): ?>
                                <tr>
                                    <th>Установлена максимальная сумма по договору, рублей</th>
                                    <td><?= round($futurePeriodCooperate->total_payment_limit, 2) ?>
                                        <?= $this->render(
                                            '../cooperate/payment-limit',
                                            [
                                                'toggleButtonClass' => '',
                                                'label' => '(изменить)',
                                                'model' => $futurePeriodCooperate,
                                            ]
                                        ); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php } ?>

                        <?php if (!empty($commitments)): ?>
                            <tr>
                                <th>Совокупная сумма подтвержденных обязательств по договору</th>
                                <td>
                                    <?= round($commitments, 2) . ' рублей'; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($summary)): ?>
                            <tr>
                                <th>Совокупная сумма оплаченных ранее счетов</th>
                                <td>
                                    <?= round($summary, 2) . ' рублей'; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>

                </div>
                <?php endif; ?>
            <?php }
        } ?>
    </div>
</div>
