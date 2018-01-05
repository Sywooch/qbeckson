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
use yii\bootstrap\Progress;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = 'Список договоров для автопролонгации';

$this->params['breadcrumbs'][] = ['label' => 'Пролонгация программ', 'url' => ['/programs/program-list-for-auto-prolongation']];
$this->params['breadcrumbs'][] = $this->title;

$js = <<<js
$('.change-auto-prolongation-checkbox').on('click', function() {
    var url = $(this).data('url');
    
    $.ajax({
        url: url,
        method: 'POST',
        data: {"Contracts": {"auto_prolongation_enabled": $(this).prop('checked') ? 1 : 0}}
    });
});

$('#change-all-auto-prolongation').on('click', function() {
    var url = $(this).data('url');

    $.ajax({
        url: url,
        method: 'POST',
        data: {"change-auto-prolongation-for-all-contracts": $(this).prop('checked') ? 1 : 0},
        success: function(data) {
            if (data.changed == 1) {
                if (data.value == 1) {
                    $('.change-auto-prolongation-checkbox').prop('checked', true);
                } else {
                    $('.change-auto-prolongation-checkbox').prop('checked', false);
                }
            }
        }
    });
});

$('.auto-prolongation-init-button').on('click', function() {
    var url = $(this).data('url'),
        contractToAutoProlongationCount = 0;

    $('.progress').show();
    $(this).prop('disabled', true);
    
    autoProlongation(url, contractToAutoProlongationCount, 1);
});

function autoProlongation(url, contractToAutoProlongationCount, isNew) {
    $.ajax({
        url: url,
        method: 'POST',
        data: {isNew: isNew},
        success: function(data) {
            console.log(data);
            if(data.remainCount >= 0) {
                if (contractToAutoProlongationCount == 0) {
                    contractToAutoProlongationCount = data.remainCount;
                }

                var reg=/([0-9]+).*/g;
                var percent = ((contractToAutoProlongationCount - data.remainCount)/contractToAutoProlongationCount * 100).toString().replace(reg, '$1');

                $('.progress-bar').css('width', percent +'%');
                $('.progress-bar').html(percent + '%');
                
                autoProlongation(url, contractToAutoProlongationCount, 0);
            } else if (data.status == 'processed') {
                $('.progress-bar').css('width', '100%');
                $('.progress-bar').html('100%');
                
                $('.auto-prolongation-init-button').hide();
                $('.auto-prolongation-cancel').hide();
                $('.auto-prolongation-init-complete').show();
                
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {allCreated: true}
                })
            }
        }
    });
}

js;
$this->registerJs($js);
?>

<div class="panel">
    <?php Modal::begin([
        'id' => 'auto-prolongation-init',
        'header' => 'Запуск автопролонгации',
        'closeButton' => false,
        'toggleButton' => [
            'label' => 'Запустить автопролонгацию',
            'class' => 'btn btn-primary',
        ],
    ]) ?>

    <p>Вы инициировали запуск автопролонгации действующих до <?= \Yii::$app->formatter->asDate($operatorSettings->current_program_date_to) ?> договоров об обучении с детьми. Обращаем Ваше внимание, что в рамках
        данной процедуры для всех отмеченных на предыдущем шаге программ будут выбраны только те группы, в которых продолжается обучение в будущем периоде (то есть договор был заключен на часть модуля), и
        только для их детей будут созданы оферты ("подтвержденные договоры"), а в случае отсутствия у Вас договора с уполномоченной организацией детей - заявки на обучение ("ожидающие подтверждения"на
        обучение), предусматривающие начало действия (обучения по договору) с <?= \Yii::$app->formatter->asDate($operatorSettings->future_program_date_from) ?>.</p>

    <?= Progress::widget(['percent' => 0, 'label' => '0%', 'options' => ['hidden' => true]]) ?>

    <br>
    <?= Html::button('Запустить', ['class' => 'btn btn-primary auto-prolongation-init-button', 'data' => ['url' => '/programs/auto-prolongation-init']]) ?>
    <?= Html::button('Отмена', ['class' => 'btn btn-danger auto-prolongation-cancel', 'onClick' => '$(".modal").modal("hide")']) ?>

    <?= Html::a('Готово', '/personal/organization-contracts', ['class' => 'btn btn-primary auto-prolongation-init-complete', 'style' => ['display' => 'none']]) ?>

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
            'header' => 'Выбрать все<br>' . Html::checkbox('', false, ['id' => 'change-all-auto-prolongation', 'data' => ['url' => Url::to('change-auto-prolongation-for-contract')]]),
            'template' => '{checkbox}',
            'buttons' => [
                'checkbox' => function ($url, $contract, $key) {
                    /** @var $contract Contracts */
                    return Html::checkbox('', $contract->auto_prolongation_enabled, [
                        'class' => 'change-auto-prolongation-checkbox',
                        'data' => [
                            'url' => Url::to(['change-auto-prolongation-for-contract', 'id' => $contract->id])
                        ]
                    ]);
                }
            ]
        ],
    ],
]); ?>
