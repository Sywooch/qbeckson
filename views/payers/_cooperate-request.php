<?php

use app\models\Cooperate;
use app\models\OperatorSettings;
use kartik\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\View;

/**
 * часть страницы отображения модального окна подачи заявки на заключение договора с плательщиком
 *
 * @var $this View
 * @var $operatorSettings OperatorSettings
 * @var Cooperate $cooperation
 */

?>

<?php Modal::begin([
    'header' => 'Направить заявку на заключение соглашения с уполномоченной организацией',
    'toggleButton' => [
        'class' => 'btn btn-primary',
        'label' => 'Направить заявку на заключение соглашения с уполномоченной организацией',
    ],
]) ?>
<p>Вы собираетесь подать заявку на заключение <?= Cooperate::documentNamesInGenitive()[$operatorSettings->document_name] ?> уполномоченной организации <?= $model->name ?></p>

<br>

<?php if ($operatorSettings->payerCanCreateFuturePeriodCooperate()): ?>
    <p>Выберите период в котором предполагаете вступление в силу Вашего договора</p>

    <?= Html::radioList('cooperate-period', [Cooperate::PERIOD_FUTURE], [
        Cooperate::PERIOD_CURRENT => 'текущий период: ' . Cooperate::periodValidityList(Cooperate::PERIOD_CURRENT),
        Cooperate::PERIOD_FUTURE => 'будущий период: ' . Cooperate::periodValidityList(Cooperate::PERIOD_FUTURE),
    ], ['separator' => "<br>", 'item' => function ($index, $label, $name, $checked, $value) {
        $result = Html::radio($name, Cooperate::PERIOD_FUTURE == $value ? true : false, [
            'label' => $label,
            'onClick' => '$(".cooperate-request-link").attr("href", $(".cooperate-request-link").data("cooperate-request-url") + "&period=' . $value .'");',
        ]);

        return $result;
    }]) ?>
<?php else: ?>
    <p>Поскольку до окончания периода программы ПФ остается более 30 дней Ваша заявка будет подана в целях заключения договора, действующего уже в текущем периоде:
        с <?= \Yii::$app->formatter->asDate($operatorSettings->current_program_date_from) ?> по <?= \Yii::$app->formatter->asDate($operatorSettings->current_program_date_to) ?></p>
<?php endif; ?>

<div class="checkbox-container">
    <?= Html::checkbox('', false, [
        'label' => 'Подтвердить намерения подачи заявки на заключение договора в соответствии с представленными выше условиями',
        'onClick' => 'showNextContainer(this);',
    ]) ?>
</div>

<div class="center" style="display: none;">
    <?= Html::a(
        'Направить заявку',
        Url::to(['cooperate/request', 'payerId' => $model->id, 'period' => $operatorSettings->payerCanCreateFuturePeriodCooperate() ? Cooperate::PERIOD_FUTURE : Cooperate::PERIOD_CURRENT]),
        [
            'class' => 'btn btn-primary cooperate-request-link',
            'data' => ['cooperate-request-url' => Url::to(['cooperate/request', 'payerId' => $model->id])],
        ]
    ); ?>
</div>

<?php Modal::end() ?>
