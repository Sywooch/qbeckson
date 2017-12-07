<?php

use app\models\forms\ContractCreatePermissionConfirmForm;
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

$this->title = 'Номиналы групп';
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
jQuery('#payers-certificate_can_use_future_balance').click(function(){
    jQuery.post('/payers/save-params', {
        'Payers[certificate_can_use_future_balance]': jQuery(this).prop('checked') ? 1 : 0
    }).done(function(data){});
});

$('.certificate-can-create-contract').on('click', function() {
    if ($(this).prop('checked') == false) {
        $('#modal-deny-to-create-contract').modal();
    } else {
        $('#modal-allow-to-create-contract').modal();
    }
});

$('.modal-dialog').on('click', function(e) {
  e.stopPropagation();
});

$('.modal').on('click', function() {
  $.ajax({
        type: 'POST',
        url: '/cert-group/index?getPermission=1', 
        data: $('#payer-settings-form').serialize(),
        success: function (data) {
            if (data == 1) {
                $('.certificate-can-create-contract').prop('checked', 'checked');
            } else {
                $('.certificate-can-create-contract').removeAttr('checked');
            }
        }
    });
});

$('.change-permission-to-contract-create').on('click', function() {    
    $.ajax({
        type: 'POST',
        url: '/cert-group/index?changePermission=1', 
        data: $('#payer-settings-form').serialize(),
        success: function (data) {
            console.log(data);
            if (data.changed == true) {
                if (data.canCreate == 1) {
                    $('.certificate-can-create-contract').prop('checked', 'checked');
                    $('#modal-allow-to-create-contract').modal('hide');
                } else {
                    $('.certificate-can-create-contract').removeAttr('checked');
                    $('#modal-deny-to-create-contract').modal('hide');
                }
            }
        }
    });
});

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

    <?= $form->field(Yii::$app->user->identity->payer, 'certificate_can_use_future_balance')->checkbox() ?>

    <div data-toogle="tooltip" title="<?= !$payer->canChangePermission() ? 'установить запрет на зачисление на обучение в текущем периоде невозможно до ' . Yii::$app->formatter->asDate(date('Y-m-d', strtotime(\Yii::$app->operator->identity->settings->current_program_date_to . '-2 Month'))) : '' ?>">
        <?= $form->field($contractCreatePermissionConfirmForm, 'certificate_can_create_contract')->checkbox(['class' => 'certificate-can-create-contract', 'disabled' => !$payer->canChangePermission()]) ?>
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
