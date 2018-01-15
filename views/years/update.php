<?php

use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

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
            <?php Pjax::begin([
                'id' => 'update-pjax'
            ]) ?>
            <p>
                Укажите дату начала и окончания реализации модуля для того, чтобы система могла предложить
                Вам расчёт максимальной стоимости программы.
            </p>
            <?php $form = ActiveForm::begin([
                'action' => ['update-request', 'id' => $module->id],
                'enableClientValidation' => false,
                'options' => [
                    'data-pjax' => true
                ],
            ]) ?>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'dateFrom')->widget(DatePicker::class, [
                        'pluginOptions' => [
                            'format' => 'dd.mm.yyyy'
                        ]
                    ])->label(false) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'dateTo')->widget(DatePicker::class, [
                        'pluginOptions' => [
                            'format' => 'dd.mm.yyyy'
                        ]
                    ])->label(false) ?>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= Html::submitButton(
                            'Пересчитать',
                            ['class' => 'btn btn-success btn-block']
                        ) ?>
                    </div>
                </div>
            </div>
            <?php $form::end() ?>
            <?php if (null !== $model->dateFrom && null !== $model->dateTo) : ?>
                <p>
                    Укажите цену модуля, которую Вы собираетесь установить.
                    Обратите внимание, что, учитывая продолжительность и график реализации модуля
                    от <?= $model->dateFrom ?> до <?= $model->dateTo ?>,
                    договор будет заключаться:
                </p>
                <p>
                    - при заключении договора в текущем периоде (с <?= Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_from) ?> до <?= Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_to) ?>) - на <?= round($model->calculateCurrentPercent()) ?>
                    % стоимости модуля;
                </p>
                <p>
                    - при заключении договора на будущий период (с <?= Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_from) ?> до <?= Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_to) ?>) - на <?= round($model->calculateFuturePercent(), 2) ?>
                    % стоимости модуля.
                </p>
                <p>
                    Номинал обеспечения сертификата детей в муниципальном районе (городском округе)
                    "<?= $module->program->municipality->name ?>"
                    в текущем периоде составляет - <?= $model->getPayer()->firstCertGroup->nominal ?> руб.
                    ожидаемый номинал в будущем периоде - <?= $model->getPayer()->firstCertGroup->nominal_f ?> руб.
                </p>
                <p>
                    В этой связи, максимальная цена модуля, которая не израсходует все средства на сертификате ребенка
                    составляет: <?= floor($model->calculateRecommendedPrice()) ?> рублей.
                </p>
                <p>
                    Обратите внимание, что если устанавливаемая Вами цена превысит нормативную стоимость, равную
                    <?= $model->getModel()->normative_price ?> рублей,
                    то заключение любого договора будет предусматривать необходимость софинансирования
                    со стороны родителей.
                </p>
                <p>
                    Учитывая максимальную цену и нормативную стоимость рекомендуем установить стоимость модуля не выше
                    <span id="recommend-price"><?= floor(min($model->getModel()->normative_price, $model->calculateRecommendedPrice())) ?></span> рублей.
                </p>
            <?php endif; ?>
            <?php if (null !== $model->dateFrom && null !== $model->dateTo) : ?>
                <?php $formConfirm = ActiveForm::begin([
                    'action' => ['update', 'id' => $module->id],
                    'enableClientValidation' => false,
                    'id' => 'confirmation-form',
                    'options' => [
                        'data-pjax' => true
                    ],
                ]) ?>
                <div class="hide">
                    <?= $formConfirm->field($model, 'dateFrom')->hiddenInput() ?>
                    <?= $formConfirm->field($model, 'dateTo')->hiddenInput() ?>
                </div>
                <?= $formConfirm->field($model, 'price')->textInput(['onKeyup' => /** @lang JavaScript */ '
                    $.ajax({
                        data: {price: $(this).val(), calculatePrice: 1},
                        method: "POST",
                        success: function (data) {
                            $(".module-price-average").html(data.modulePriceAverage);
                        }
                    });
                ']) ?>

                <p class="module-price-average"></p>

                <?php if ($model->price > $model->getModel()->normative_price) : ?>
                    <?= $formConfirm->field($model, 'firstConfirm')->checkbox() ?>
                <?php endif; ?>
                <?php if ($model->price > $model->calculateRecommendedPrice()) : ?>
                    <?= $formConfirm->field($model, 'secondConfirm')->checkbox() ?>
                <?php endif; ?>
                <div class="form-group">
                    <?= Html::submitButton(
                        'Сохранить цену',
                        ['class' => 'btn btn-success btn-block']
                    ) ?>
                </div>
                <?php $formConfirm::end(); ?>
            <?php endif; ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>

