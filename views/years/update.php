<?php

use app\helpers\CalculationHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\models\forms\ModuleUpdateForm */
/* @var $form ActiveForm */
/* @var $settings \app\models\OperatorSettings */

$module = $model->getModel();
$this->title = 'Установить цену: ' . $module->program->name . ' ' . $module->year . ' модуль';
$this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['personal/organization-programs']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="years-update">
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <?php if ([] !== $module->groups) : ?>
                <?php /** @var \app\models\Groups $group */ $group = $module->groups[0]; ?>
                <p>
                    Укажите цену модуля, которую Вы собираетесь установить.
                    Обратите внимание, что, учитывая продолжительность и график реализации модуля
                    от <?= $group->datestart ?> до <?= $group->datestop ?>,
                    договор будет заключаться:
                </p>
                <?php
                /*
                 * TODO можно перенести в форму
                 *
                 * (расчет: если дата конца текущего периода < дата начала группы, то 0%, иначе -
                 * (дата конца текущего текущего периода - дата начала группы +1)/(дата конца группы - дата начала группы +1))
                 */
                if (strtotime($settings->current_program_date_to) < strtotime($group->datestart)) {
                    $currentPercent = 0;
                } else {
                    $dateFrom = max(strtotime($group->datestart), strtotime($settings->current_program_date_from));
                    $dateTo = min(strtotime($group->datestop), strtotime($settings->current_program_date_to));
                    $currentPercent = CalculationHelper::daysBetweenDates(
                        date('Y-m-d', $dateFrom),
                        date('Y-m-d', $dateTo)
                    ) / CalculationHelper::daysBetweenDates($group->datestop, $group->datestart) * 100;
                }
                ?>
                <p>
                    - при заключении договора в текущем периоде - на <?= round($currentPercent) ?>% стоимости модуля;
                </p>
                <?php
                /*
                 * TODO можно перенести в форму
                 *
                 * (расчет: если дата конца группы < дата начала будущего периода, то 0%, иначе:
                 * (дата конца группы - дата начала будущего периода+1)/(дата конца группы - дата начала группы +1) )
                */
                if (strtotime($settings->future_program_date_from) > strtotime($group->datestop)) {
                    $futurePercent = 0;
                } else {
                    $dateFrom = max(strtotime($group->datestart), strtotime($settings->future_program_date_from));
                    $dateTo = min(strtotime($group->datestop), strtotime($settings->future_program_date_to));
                    $futurePercent = CalculationHelper::daysBetweenDates(
                        date('Y-m-d', $dateFrom),
                        date('Y-m-d', $dateTo)
                    ) / CalculationHelper::daysBetweenDates($group->datestop, $group->datestart) * 100;
                }
                ?>
                <p>
                    - при заключении договора на будущий период - на <?= round($futurePercent, 2) ?>% стоимости модуля.
                </p>
                <?php
                /*
                 * TODO можно перенести в форму
                 *
                 * (минимум из максимальных цен для текущего и будущего периода: мин(номинал текущий/долю программы в
                 * текущем периоде; номинал будущего периода/доля программы в будущем периоде). Округляем вниз до рубля)
                 */
                $certGroup = $module->program->municipality->payer->firstCertGroup;
                if ($currentPercent === 0 && $futurePercent !== 0) {
                    $price = $certGroup->nominal_f / $futurePercent * 100;
                } elseif ($futurePercent === 0 && $currentPercent !== 0) {
                    $price = $certGroup->nominal / $currentPercent * 100;
                } else {
                    $price = min(
                        $certGroup->nominal / $currentPercent * 100,
                        $certGroup->nominal_f / $futurePercent * 100
                    );
                }
                ?>
                <p>
                    Номинал обеспечения сертификата детей в муниципальном районе (городском округе)
                    "<?= $module->program->municipality->name ?>"
                    в текущем периоде составляет - <?= $certGroup->nominal ?> руб.,
                    ожидаемый номинал в будущем периоде - <?= $certGroup->nominal_f ?> руб.
                </p>
                <p>
                    В этой связи, максимальная цена модуля, которая не израсходует все средства на сертификате ребенка
                    составляет: <?= floor($price) ?> рублей.
                </p>
                <p>
                    Обратите внимание, что если устанавливаемая Вами цена превысит нормативную стоимость,
                    то заключение любого договора будет предусматривать необходимость софинансирования со стороны родителей.
                </p>
            <?php else : ?>
                <p>
                    Вы собираетесь установить цену. Для того, чтобы мы могли подсказать
                    Вам ограничения для устанавливаемой цены - пожалуйста, добавьте хотя бы одну группу в модуль
                    (Важно правильно определить срок реализации программы)
                </p>
            <?php endif; ?>
            <?php $form = ActiveForm::begin([
                'enableAjaxValidation' => true,
                'enableClientValidation' => false
            ]) ?>
            <?= $form->field($model, 'price')->textInput() ?>
            <?= $form->field($model, 'confirm')->checkbox() ?>
            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-block']) ?>
            </div>
            <?php $form::end(); ?>
        </div>
    </div>
</div>
