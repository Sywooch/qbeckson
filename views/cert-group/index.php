<?php

use app\models\forms\ContractCreatePermissionConfirmForm;
use app\models\OperatorSettings;
use app\models\Payers;
use kartik\grid\EditableColumn;
use kartik\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CertGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $payer Payers */
/* @var $contractCreatePermissionConfirmForm ContractCreatePermissionConfirmForm */
/* @var $operatorSettings OperatorSettings */
/* @var $cooperateCurrentPeriodCount integer */
/* @var $cooperateFuturePeriodCount integer */

$this->title = 'Номиналы групп';
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
jQuery('#payers-certificate_can_use_future_balance').click(function(){
    if ($(this).prop('checked') == true) {
        $('#certificate-can-use-future-balance-modal').modal();
    } else {
        $('#certificate-cant-use-future-balance-modal').modal();
    }
});

$('.certificate-can-use-future-balance').on('click', function() {
    checkCertificateCanUseFutureBalance(true);
});

$('.certificate-can-create-contract').on('click', function() {
    if ($(this).prop('checked') == false) {
        $('#modal-deny-to-create-contract').modal();
    } else {
        $('#modal-allow-to-create-contract').modal();
    }
});

$('.change-permission-to-contract-create').on('click', function() {    
    $.ajax({
        type: 'POST',
        url: '/cert-group/index?changePermission=1', 
        data: $('#payer-settings-form').serialize(),
        success: function (data) {
            if (data.changed == true) {
                if (data.canCreate == 1) {
                    $('.certificate-can-create-contract').prop('checked', true);
                    $('#modal-allow-to-create-contract').modal('hide');
                } else {
                    $('.certificate-can-create-contract').prop('checked', false);
                    $('#modal-deny-to-create-contract').modal('hide');
                }
            }
        }
    });
});

$('.modal-dialog').on('click', function(e) {
    e.stopPropagation();
});

$('.modal').on('click', function() {
    checkContractCreatePermission();
    checkCertificateCanUseFutureBalance(false);
});

$('.close').on('click', function() {
    var modal = $(this).parents('.modal').first();
    
    checkContractCreatePermission();
    checkCertificateCanUseFutureBalance(false);
    
    modal.modal('hide');    
});

function checkContractCreatePermission() {
    $.ajax({
        type: 'POST',
        url: '/cert-group/index?getPermission=1', 
        data: $('#payer-settings-form').serialize(),
        success: function (data) {
            if (data == 1) {
                $('.certificate-can-create-contract').prop('checked', true);
            } else {
                $('.certificate-can-create-contract').prop('checked', false);
            }
        }
    });
}

function checkCertificateCanUseFutureBalance(change) {
    var data = change ? {'Payers[certificate_can_use_future_balance]': $('#payers-certificate_can_use_future_balance').prop('checked') ? 1 : 0, change: 1} : {change: 0};
    
    $.ajax({
        url: '/payers/save-params',
        data: data,
        method: 'POST',
        success: function (data) {
            if (data.certificate_can_use_future_balance == 1) {
                $('#payers-certificate_can_use_future_balance').prop('checked', true);
            } else {
                $('#payers-certificate_can_use_future_balance').prop('checked', false);
            }
    
            $('#certificate-can-use-future-balance-modal').modal('hide');
            $('#certificate-cant-use-future-balance-modal').modal('hide');
        }
    });
}

JS;
$this->registerJs($js);

?>
<div class="cert-group-index col-md-10 col-md-offset-1">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            [
                'class' => EditableColumn::class,
                'attribute' => 'group',
                'pageSummary' => true,
                'editableOptions' => [
                    'asPopover' => false,
                    'submitButton' => [
                        'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                        'class' => 'btn btn-sm btn-success',
                    ],
                ],
                'readonly' => function ($model, $key, $index, $widget) {
                    if ($model->is_special) {
                        return true;
                    }

                    return false;
                },
            ],
            [
                'class' => EditableColumn::class,
                'attribute' => 'nominal',
                'label' => '<span title="Текущий период: с ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_from) . ' до ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_to) . '">Номинал</span>',
                'encodeLabel' => false,
                'pageSummary' => true,
                'editableOptions' => [
                    'submitButton' => [
                        'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                        'class' => 'btn btn-sm btn-success',
                    ],
                    'afterInput' => function ($form, $widget) {
                        echo '<br>' .
                            Html::passwordInput(
                                'password',
                                '',
                                ['class' => 'form-control', 'placeholder' => 'Введите пароль']
                            );
                    }
                ],
                'readonly' => function ($model, $key, $index, $widget) {
                    if ($model->is_special) {
                        return true;
                    }

                    return false;
                },
            ],
            [
                'class' => EditableColumn::class,
                'attribute' => 'nominal_f',
                'label' => '<span title="Будущий период: с ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_from) . ' до ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_to) . '">Номинал будущего периода</span>',
                'encodeLabel' => false,
                'pageSummary' => true,
                'editableOptions' => [
                    'submitButton' => [
                        'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                        'class' => 'btn btn-sm btn-success',
                    ],
                    'afterInput' => function ($form, $widget) {
                        echo '<br>' .
                            Html::passwordInput(
                                'password',
                                '',
                                ['class' => 'form-control', 'placeholder' => 'Введите пароль']
                            );
                    }
                ],
                'readonly' => function ($model, $key, $index, $widget) {
                    if ($model->is_special) {
                        return true;
                    }

                    return false;
                },
            ],
            'countActualCertificates',
            'sumCertificatesNominals',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'nominals_limit',
                'pageSummary' => false,
                'editableOptions' => [
                    'asPopover' => false,
                    'submitButton' => [
                        'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                        'class' => 'btn btn-sm btn-success',
                    ],
                ],
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'amount',
                'pageSummary' => false,
                'editableOptions' => [
                    'asPopover' => false,
                    'submitButton' => [
                        'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                        'class' => 'btn btn-sm btn-success',
                    ],
                ],
                'readonly' => function ($model, $key, $index, $widget) {
                    if ($model->is_special) {
                        return true;
                    }

                    return false;
                },
            ],
        ],
    ]); ?>

    <?php $form = ActiveForm::begin(['id' => 'payer-settings-form', 'enableAjaxValidation' => true]); ?>
    <?= $form->field($payer, 'days_to_first_contract_request')->textInput() ?>
    <?= $form->field($payer, 'days_to_contract_request_after_refused')->textInput() ?>
    <p>*В случае если в течение указанных сроков для сертификата ПФ не будут созданы новые заявки на обучение, то сертификат подлежит переводу в тип "сертификат учета".</p>
    <?= Html::submitButton('сохранить', ['class' => 'btn btn-primary']) ?>

    <br>
    <br>

    <div class="checkbox">
        <?= Html::checkbox('', $payer->certificate_can_use_future_balance, [
            'id' => 'payers-certificate_can_use_future_balance',
            'label' => $payer->getAttributeLabel('certificate_can_use_future_balance'),
        ]) ?>
    </div>

    <?php Modal::begin([
        'id' => 'certificate-can-use-future-balance-modal',
        'header' => 'Установите возможность заключения договоров за счет средств сертификатов, предусмотренных на период от 01.01.2018 до 31.12.2018',
    ]) ?>

    <p>Вы собираетесь установить возможность заключения договоров на обучения для Ваших детей с поставщиками
        образовательных услуг на
        период <?= 'с ' . \Yii::$app->formatter->asDate($operatorSettings->future_program_date_from) . ' по ' . \Yii::$app->formatter->asDate($operatorSettings->future_program_date_to) ?>
        . Обратите внимание, что такая возможность появится лишь у тех поставщиков услуг для которых Вы указали сведения
        о Договоре с Вами, действующим
        с <?= \Yii::$app->formatter->asDate($operatorSettings->future_program_date_from) ?>. Сейчас таких
        организаций <?= $cooperateFuturePeriodCount ?>, а в данном периоде их <?= $cooperateCurrentPeriodCount ?>. Вам
        необходимо как можно скорее заключить необходимые Договоры на будущий период с теми поставщиками образовательных
        услуг, с которыми Вы их еще не заключили.</p>

    <div class="checkbox-container">
        <?= Html::checkbox('', false, [
            'label' => 'задача понятна и/или уже решена',
            'onClick' => 'showNextContainer(this);',
        ]) ?>
    </div>

    <div style="display: none">
        <?= Html::button('установить возможность заключения договоров в будущем периоде', ['class' => 'btn btn-primary certificate-can-use-future-balance']) ?>
    </div>

    <?php Modal::end() ?>

    <?php Modal::begin([
        'id' => 'certificate-cant-use-future-balance-modal',
        'header' => 'Установите возможность заключения договоров за счет средств сертификатов, предусмотренных на период от 01.01.2018 до 31.12.2018',
    ]) ?>

    <div class="checkbox-container">
        <?= Html::checkbox('', false, [
            'label' => 'Вы уверены, что хотите приостановить возможность заключения договоров на обучение, действующих с ' . Yii::$app->formatter->asDate(date('Y-m-d', strtotime(\Yii::$app->operator->identity->settings->future_program_date_from))) . '?',
            'onClick' => 'showNextContainer(this);',
        ]) ?>
    </div>

    <div style="display: none">
        <?= Html::button('приостановить возможность заключения договоров в будущем периоде', ['class' => 'btn btn-primary certificate-can-use-future-balance']) ?>
    </div>

    <?php Modal::end() ?>

    <div data-toogle="tooltip"
         title="<?= !$payer->canChangeContractCreatePermission() ? 'установить запрет на зачисление на обучение в текущем периоде невозможно до ' . Yii::$app->formatter->asDate(date('Y-m-d', strtotime(\Yii::$app->operator->identity->settings->current_program_date_to . '-2 Month'))) : '' ?>">
        <?= $form->field($contractCreatePermissionConfirmForm, 'certificate_can_use_current_balance', ['enableAjaxValidation' => false])->checkbox(['class' => 'certificate-can-create-contract', 'disabled' => !$payer->canChangeContractCreatePermission()]) ?>
    </div>

    <!-- окно для отмены создания договоров -->
    <?php Modal::begin([
        'id'     => 'modal-deny-to-create-contract',
        'header' => 'Вы уверены, что хотите установить запрет на заключение новых договоров с ' . Yii::$app->formatter->asDate(date('Y-m-d', strtotime('+2 Days'))) . '. После установки запрета на зачисление, в текущем периоде вернуть возможность зачисления будет уже невозможно',
    ]) ?>
    <div class="checkbox-container">
        <?= $form->field($contractCreatePermissionConfirmForm, 'changePermissionConfirm', ['enableAjaxValidation' => false])->checkbox(['onClick' => 'showNextContainer(this);']); ?>
    </div>

    <div style="display: none">
        <p>Введите пароль, который Вы используете для входа в личный кабинет</p>

        <?= $form->field($contractCreatePermissionConfirmForm, 'password')->passwordInput(); ?>

        <?= Html::Button('подтвердить', ['class' => 'change-permission-to-contract-create btn btn-primary']) ?>
    </div>

    <?php Modal::end() ?>

    <!-- окно для разрешения создания договоров -->
    <?php Modal::begin([
        'id'     => 'modal-allow-to-create-contract',
        'header' => 'После сохранения будет снят запрет на заключение новых договоров',
    ]) ?>

    <?= Html::Button('подтвердить', ['class' => 'change-permission-to-contract-create btn btn-primary']) ?>

    <?php Modal::end() ?>

    <?php ActiveForm::end(); ?>

</div>
