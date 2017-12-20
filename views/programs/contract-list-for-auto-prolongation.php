<?php

/**
 * страница со списком договоров по программам, отмеченным для автопролонгации
 *
 * @var $this View
 * @var $contractDataProvider ActiveDataProvider
 * @var $operatorSettings OperatorSettings
 */

use app\models\Contracts;
use app\models\OperatorSettings;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = 'Список договоров для автопролонгации';

$this->params['breadcrumbs'][] = ['label' => 'Пролонгация программ', 'url' => ['/programs/program-list-for-auto-prolongation']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $form = ActiveForm::begin([
    'action' => '/programs/auto-prolongation-init',
    'method' => 'POST',
]) ?>
<div class="panel">
    <?php Modal::begin([
        'id' => 'auto-prolongation-init',
        'header' => 'Запуск автопролонгации',
        'toggleButton' => [
            'label' => 'Запустить авто пролонгацию',
            'class' => 'btn btn-primary',
        ],
    ]) ?>

    <p>Вы инициировали запуск автопролонгации действующих до <?= \Yii::$app->formatter->asDate($operatorSettings->current_program_date_to) ?> договоров об обучении с детьми. Обращаем Ваше внимание, что в рамках
        данной процедуры для всех отмеченных на предыдущем шаге программ будут выбраны только те группы, в которых продолжается обучение в будущем периоде (то есть договор был заключен на часть модуля), и
        только для их детей будут созданы оферты ("подтвержденные договоры"), а в случае отсутствия у Вас договора с уполномоченной организацией детей - заявки на обучение ("ожидающие подтверждения"на
        обучение), предусматривающие начало действия (обучения по договору) с <?= \Yii::$app->formatter->asDate($operatorSettings->future_program_date_from) ?>.</p>

    <?= Html::submitButton('Запустить', ['class' => 'btn btn-primary auto-prolongation-init-button', 'data' => ['url' => '/programs/auto-prolongation-init']]) ?>

    <?php Modal::end() ?>
</div>

<?= GridView::widget([
    'dataProvider' => $contractDataProvider,
    'columns' => [
        [
            'class' => DataColumn::className(),
            'header' => 'Название группы',
            'content' => function ($contract) {
                /** @var Contracts $contract */
                return $contract->group->name;
            }
        ],
        [
            'class' => DataColumn::className(),
            'header' => 'Номер сертификата',
            'content' => function ($contract) {
                /** @var Contracts $contract */
                return $contract->certificate->number;
            }
        ],
        [
            'class' => DataColumn::className(),
            'header' => 'ФИО',
            'content' => function ($contract) {
                /** @var Contracts $contract */
                return $contract->certificate->fio_child;
            }
        ],
        'number',
        [
            'class' => ActionColumn::className(),
            'header' => 'авто пролонгация',
            'template' => '{checkbox}',
            'buttons' => [
                'checkbox' => function ($url, $contract, $key) {
                    /** @var $contract Contracts */
                    return Html::checkbox($contract->id, true, [
                        'class' => 'contract-for-prolongation-checkbox',
                        'value' => $contract->id,
                    ]);
                }
            ]
        ],
    ],
]); ?>

<?php $form->end() ?>
