<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $certificate \app\models\Certificates */

$this->title = 'Персональная информация';
$this->params['breadcrumbs'][] = ['label' => 'Персональная информация'];
?>
<br>
<div class="container-fluid col-md-10 col-md-offset-1">
    <div class="row">
        <div class="col-md-7">
            <h2><?= $certificate->fio_child ?></h2>
            <p class="biglabel">Номер сертификата <strong><?= $certificate->number ?></strong></p>
            <p class="biglabel">ФИО законного представителя <strong><?= $certificate->fio_parent ?></strong></p>
            <br>
            <br>
            <p>
                <?= Html::a(
                    'Редактировать',
                    ['/certificates/edit', 'id' => $certificate->id],
                    ['class' => 'btn btn-success']
                ) ?>
                <?= Html::a(
                    'Изменить пароль',
                    ['/certificates/password'],
                    ['class' => 'btn btn-success']
                ) ?>
            </p>
        </div>
        <!--<div class="well col-md-5 text-center">Текущий сертификат: <b><?= $certificate->textType ?></b></div>-->
        <div class="col-md-5">
            <ul class="nav nav-tabs nav-justified" role="tablist">
                <li role="presentation"><a href="#past" aria-controls="profile" role="tab" data-toggle="tab">Прошлый</a>
                </li>
                <li role="presentation" class="active"><a href="#current" aria-controls="home" role="tab"
                                                          data-toggle="tab"
                                                          title="Текущий баланс: с <?= Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_from) ?> до <?= Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_to) ?>">Текущий</a>
                </li>
                <li role="presentation"><a href="#future" aria-controls="messages" role="tab" data-toggle="tab"
                                           title="Будущий баланс: с <?= Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_from) ?> до <?= Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_to) ?>">Будущий</a>
                </li>
            </ul>
            <div class="well text-center tab-content">
                <div role="tabpanel" class="tab-pane" id="past">
                    <p class="lead">Номинал сертификата в прошлом периоде<br>
                        <strong class="bignumbers"><?= $certificate->nominal_p ?></strong>
                    </p>
                    <p class="lead">Осталось средств в прошлом периоде<br>
                        <strong class="bignumbers"><?= $certificate->balance_p ?></strong>
                    </p>
                    <p class="lead">Зарезервировано на оплату договоров в прошлом периоде<br>
                        <strong class="bignumbers"><?= round($certificate->rezerv_p, 2) ?></strong>
                    </p>
                </div>
                <div role="tabpanel" class="tab-pane active" id="current">
                    <p class="lead">Номинал сертификата<br>
                        <strong class="bignumbers"><?= $certificate->nominal ?></strong>
                    </p>
                    <p class="lead">Осталось средств<br>
                        <strong class="bignumbers"><?= $certificate->balance ?></strong>
                    </p>
                    <p class="lead">Зарезервировано на оплату договоров<br>
                        <strong class="bignumbers"><?= round($certificate->rezerv, 2) ?></strong>
                    </p>
                </div>
                <div role="tabpanel" class="tab-pane" id="future">
                    <?php if ($certificate->payer->certificate_can_use_future_balance > 0): ?>
                        <p class="lead">Номинал сертификата на будущий период<br>
                            <strong class="bignumbers"><?= $certificate->nominal_f ?></strong>
                        </p>
                        <p class="lead">Осталось средств на будущий период<br>
                            <strong class="bignumbers"><?= $certificate->balance_f ?></strong>
                        </p>
                        <p class="lead">Зарезервировано на оплату договоров на будущий период<br>
                            <strong class="bignumbers"><?= round($certificate->rezerv_f, 2) ?></strong>
                        </p>
                    <?php else: ?>
                        <p>Номинал на будущий период пока не определен.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div>
        <?php
        /*if (Yii::$app->user->can('certificate') && $certificate->canChangeGroup && !empty($certificate->getCertificateGroupQueues($certificate->id)->count())) {
            echo '<div class="alert alert-danger" role="alert">Вы находитесь в очереди на смену группы сертификата. Пожалуйста, подождите.</div>';
        } elseif (Yii::$app->user->can('certificate') && $certificate->canChangeGroup) {
            echo '<br />';
            $form = ActiveForm::begin();
            if ($certificate->certGroup->is_special > 0) {
                $certificate->cert_group = $certificate->possibleCertGroup->id;
            } else {
                $certificate->cert_group = $certificate->payers->getCertGroups(1)->one()->id;
            }
            echo $form->field($certificate, 'cert_group')->hiddenInput()->label(false);
            echo '<p>Сменить тип сертификата на ' . Html::submitButton($certificate->getTextType(true), ['class' => 'btn btn-primary', 'onClick' => 'if (!confirm("Уверены?")) return false;']) . '</p>';
            ActiveForm::end();
        }*/
        ?>
    </div>
</div>
