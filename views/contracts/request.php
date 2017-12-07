<?php

use app\models\Contracts;
use app\models\forms\ContractRequestForm;
use kartik\widgets\DatePicker;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model ContractRequestForm */
/* @var $confirmForm \app\models\forms\ContractConfirmForm */
/* @var $form ActiveForm */
/* @var $contract \app\models\Contracts */
/* @var $contractRequestFormValid boolean */
/** @var $groupId integer */
/** @var $certificateId integer */

$this->title = 'Подать заявку на получение образовательных услуг';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin() ?>
<div class="contract-request">
    <div id="request-box">
        <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => true,
            'validationUrl' => \yii\helpers\Url::to([
                'validate-request',
                'groupId' => $groupId,
                'certificateId' => $certificateId,
            ]),
            'options' => [
                'data-pjax' => true
            ]
        ]); ?>
        <div class="row">
            <div class="col-md-offset-3 col-md-6">
                <p class="lead"><?= ($contract ? $contract->group->datestart : null > date('Y-m-d')) ? 'Выберите дату начала обучения по договору:' : 'Обратите внимание! Обучение в группе уже началось. Система предлагает вам записаться с завтрашнего дня, но вы можете поменять дату начала обучения ниже:' ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-3 col-md-4">
                <?= $form->field($model, 'dateFrom')->widget(DatePicker::class, [
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy'
                    ]
                ])->label(false) ?>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <?= Html::submitButton('Подсчитать', ['class' => 'btn btn-success btn-block']) ?>
                </div>
            </div>
        </div>
        <?php $form::end(); ?>
    </div>
    <div id="confirm-box">
        <?php if ($contractRequestFormValid) : ?>
            <?php $confirm = ActiveForm::begin([
                'options' => [
                    'data-pjax' => true
                ]
            ]) ?>
            <div class="row">
                <div class="col-md-offset-2 col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <p>
                                Вы хотите записаться на программу
                                <?= $contract->program->name ?> (<?= $contract->module->getShortName() ?>)
                            </p>
                            <br>
                            <p>Организация: <?= $contract->organization->name ?></p>
                            <p>Место проведения: <?= $contract->module->mainAddress ? $contract->module->mainAddress->address : '' ?></p>
                            <p>Дата начала: <?= Yii::$app->formatter->asDate($contract->group->datestart) ?></p>
                            <p>Дата окончания: <?= Yii::$app->formatter->asDate($contract->group->datestop) ?></p>
                            <br>
                            <p>
                                Договор заключается на период с
                                <?= Yii::$app->formatter->asDate($contract->start_edu_contract) ?> по
                                <?= Yii::$app->formatter->asDate($contract->stop_edu_contract) ?>
                                (будут использованы средства, предусмотренные на <?= ($contract->start_edu_contract <= Yii::$app->operator->identity->settings->current_program_date_to) ? 'текущий' : 'будущий' ?> период)
                            </p>
                            <br>
                            <p>
                                В первый месяц с сертификата будет списано
                                <?= $contract->payer_first_month_payment ?> руб.
                            </p>
                            <?php if ($contract->prodolj_m > 1) : ?>
                                <p>
                                    В последующие месяцы с сертификата будет производиться
                                    ежемесячное списание в размере
                                    <?= $contract->payer_other_month_payment ?> руб.
                                </p>
                            <?php endif; ?>
                            <p>
                                Всего с сертификата будет списано
                                <?= $contract->funds_cert ?> руб. Остаток сертификата составит
                                <?= $contract->period == Contracts::CURRENT_REALIZATION_PERIOD ?
                                    $contract->certificate->balance - $contract->funds_cert :
                                    $contract->certificate->balance_f - $contract->funds_cert;
                                ?> руб.
                            </p>
                            <br>
                            <?php if ($contract->all_parents_funds > 0) : ?>
                                <p>
                                    В первый месяц потребуется доплата с Вашей стороны в размере
                                    <?= round($contract->parents_first_month_payment, 2) ?> руб.
                                </p>
                                <?php if ($contract->prodolj_m > 1) : ?>
                                    <p>
                                        В последующие месяцы потребуется ежемесячная доплата с Вашей стороны в размере
                                        <?= $contract->parents_other_month_payment ?> руб.
                                    </p>
                                <?php endif; ?>
                                <p>
                                    Оплата за счёт Ваших личных средств составит
                                    <?= $contract->all_parents_funds ?> руб.
                                </p>
                                <br>
                            <?php endif; ?>
                            <p>Общая стоимость программы: <?= $contract->all_funds ?> руб.</p>
                            <br>
                            <?php if ($contract->all_parents_funds) : ?>
                                <?= $confirm->field($confirmForm, 'secondConfirmation')->checkbox(); ?>
                            <?php endif; ?>
                            <?= $confirm->field($confirmForm, 'thirdConfirmation')->checkbox(); ?>
                            <hr>
                            <?= Html::a(
                                'Отменить',
                                ['reject-request', 'id' => $contract->id],
                                ['class' => 'btn btn-danger']
                            ) ?>
                            <?php Modal::begin([
                                'header' => false,
                                'id' => 'confirmation-modal',
                                'toggleButton' => [
                                    'tag' => 'a',
                                    'label' => 'Направить заявку',
                                    'class' => 'btn btn-primary'
                                ],
                                'clientOptions' => ['backdrop' => false]
                            ]) ?>
                            <p>
                                Вы собираетесь подать заявку на обучение, после чего средства на Вашем сертификате
                                будут зарезервированы для оплаты будущего договора.
                                Пожалуйста подтвердите Ваше информированное согласие с условиями подачи заявки.
                            </p>
                            <?= $confirm->field($confirmForm, 'firstConfirmation')->checkbox(); ?>
                            <hr>
                            <div class="form-group">
                                <?= Html::submitButton(
                                    'Направить заявку поставщику образовательных услуг',
                                    ['class' => 'btn btn-success btn-block']
                                ) ?>
                            </div>
                            <?php Modal::end() ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php $confirm::end() ?>
        <?php endif; ?>
    </div>
</div>
<?php Pjax::end() ?>
