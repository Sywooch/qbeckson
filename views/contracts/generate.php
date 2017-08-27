<?php

use app\models\Cooperate;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Payers;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */

$this->title = 'Генерировать договор';
$this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/organization-contracts']];
$this->params['breadcrumbs'][] = $this->title;

/** @var \app\models\OperatorSettings $operatorSettings */
$operatorSettings = Yii::$app->operator->identity->settings;
?>
<div class="contracts-create col-md-10 col-md-offset-1">

    <div>
        <?php $form = ActiveForm::begin(); ?>
            <?php if ($model->all_parents_funds > 0) : ?>
                <div class="well">
                    <p class="lead">
                        В рамках договора предусмотрено софинансирование со стороны Заказчика. Пожалуйста укажите порядок взаимодействия с ним
                    </p>
                    <?= $form->field($model, 'sposob')->dropDownList($model::sposobs()); ?>
                    <p class="lead">Выберите порядок оплаты услуг в увязке с посещаемостью</p>
                    <?= $form->field($model, 'payment_order')->dropDownList($model::paymentOrders())->label(false); ?>
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                </div>
            <?php endif; ?>
        <?php ActiveForm::end(); ?>
    <?php
    $cooperate = (new \yii\db\Query())
        ->select(['number', 'date'])
        ->from('cooperate')
        ->where(['organization_id' => $model->organization_id])
        ->andWhere(['payer_id' => $model->payer_id])
        ->andWhere(['status' => 1])
        ->one();

    $date_cooperate = explode('-', $cooperate['date']);

    $payer = Payers::findOne($model->payer_id);
    ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <p class="lead">В договоре будет предусмотрен следующий порядок оплаты:</p>
            <p>
                Полная стоимость образовательной услуги за период обучения по Договору составляет <?= floor($model->all_funds) ?> руб.
                <?= round(($model->all_funds - floor($model->all_funds)) * 100) ?> коп., в том числе:<br>
            </p>
            <ul>
                <li>
                    Будет оплачено за счет средств сертификата дополнительного образования Обучающегося - <?= floor($model->funds_cert) ?> руб.
                    <?= round(($model->funds_cert - floor($model->funds_cert)) * 100) ?> коп.
                </li>
                <?php if ($model->all_parents_funds > 0) : ?>
                    <li>
                        Будет оплачено за счет средств Заказчика - <?= floor($model->all_parents_funds) ?> руб.
                        <?= round(($model->all_parents_funds - floor($model->all_parents_funds)) * 100) ?> коп.
                    </li>
                <?php endif; ?>
            </ul>
            <p>Оплата за счет средств сертификата осуществляется в рамках договора <?= $operatorSettings->document_name === Cooperate::DOCUMENT_NAME_FIRST ? 'о возмещении затрат' : 'об оплате дополнительного образования' ?> № <?= $cooperate['number'] ?> от <?= $date_cooperate[2] ?>.<?= $date_cooperate[1] ?>.<?= $date_cooperate[0] ?> заключенного между Исполнителем и <?= $payer->name_dat ?> (далее – Соглашение, Уполномоченная организация) ежемесячно не позднее 10-го числа месяца, следующего за месяцем оплаты в размере:</p>
            <ul>
                <li><?= floor($model->payer_first_month_payment) ?> руб.
                    <?= round(($model->payer_first_month_payment - floor($model->payer_first_month_payment)) * 100) ?> коп. - за первый месяц периода обучения по Договору</li>
                <li><?= floor($model->payer_other_month_payment) ?> руб.
                    <?= round(($model->payer_other_month_payment - floor($model->payer_other_month_payment)) * 100) ?> коп. - за каждый последующий месяц периода обучения по Договору</li>
            </ul>
            <?php if ($model->all_parents_funds > 0) : ?>
                <p>Заказчик осуществляет оплату ежемесячно не позднее 10-го числа месяца, следующего за месяцем оплаты в размере:</p>
                <ul>
                    <li><?= floor($model->parents_first_month_payment) ?> руб.
                        <?= round(($model->parents_first_month_payment - floor($model->parents_first_month_payment)) * 100) ?> коп. - за первый месяц периода обучения по Договору</li>
                    <li><?= floor($model->parents_other_month_payment) ?> руб.
                        <?= round(($model->parents_other_month_payment - floor($model->parents_other_month_payment)) * 100) ?> коп. - за каждый последующий месяц периода обучения по Договору</li>
                </ul>
            <?php endif; ?>
            <p>
                Оплата за счет средств сертификата за месяц периода обучения по Договору осуществляется в полном объеме
                при условии, если по состоянию на первое число соответствующего месяца действие настоящего Договора не
                прекращено, независимо от фактического посещения Обучающимся занятий, предусмотренных учебным планом
                Программы в соответствующем месяце.
            </p>
        </div>
    </div>
    <div class="form-group">
        <?= Html::a('Отмена', Url::to(['contracts/verificate', 'id' => $model->id]), ['class' => 'btn btn-danger']); ?>
        <?= Html::a('Предпросмотр договора', Url::to(['contracts/mpdf', 'id' => $model->id]), ['class' => 'btn btn-primary']); ?>
        <?php if ($user->organization->hasEmptyInfo()): ?>
        <p class="text-danger">Заполните "данные для договора" (меню Информация - Сведения об организации)!</p>
        <?php else: ?>
        <?php Modal::begin([
            'header' => false,
            'toggleButton' => [
                'tag' => 'a',
                'label' => 'Направить договор заказчику',
                'class' => 'btn btn-success'
            ],
        ]) ?>
            <p class="lead text-justify">
                Вы действительно готовы направить оферту на заключение договора? Заявка будет переведена в раздел "подтвержденные",
                будет сформирован договор-оферта, который будет храниться по адресу:
                <?= $model->fullUrl ?>
            </p>
            <?= Html::a('Подтвердить', ['contracts/ok', 'id' => $model->id], ['class' => 'btn btn-success btn-block']) ?>
        <?php Modal::end() ?>
        <?php endif; ?>
    </div>

</div>
