<?php

/**
 * страница со списком программ для пролонгации
 *
 * @var $this View
 * @var $programDataProvider ActiveDataProvider
 * @var $operatorSettings OperatorSettings
 * @var $program Programs
 */

use app\models\OperatorSettings;
use app\models\Programs;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = 'Пролонгация программ';

$this->params['breadcrumbs'][] = $this->title;

$js = <<<js
$('.change-auto-prolongation-checkbox').on('click', function() {
    var url = $(this).data('url');
    
    $.ajax({
        url: url,
        method: 'POST',
        data: {"Programs": {"auto_prolongation_enabled": $(this).prop('checked') ? 1 : 0}}
    });
});

$('#change-all-auto-prolongation').on('click', function() {
    var url = $(this).data('url');

    $.ajax({
        url: url,
        method: 'POST',
        data: {"change-auto-prolongation-for-all-programs": $(this).prop('checked') ? 1 : 0},
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

js;
$this->registerJs($js);

?>

<div class="panel">
    <?php Modal::begin([
        'id' => 'auto-prolongation-init',
        'header' => 'Указать договора для автопролонгации',
        'toggleButton' => [
            'label' => 'Запустить автопролонгацию',
            'class' => 'btn btn-primary',
        ],
    ]) ?>

    <p>Перейдите на следующую страницу для выбора программ для автопролонгации.</p>

    <?= Html::a('Указать договора для автопролонгации', 'contract-list-for-auto-prolongation', ['class' => 'btn btn-primary']) ?>

    <?php Modal::end() ?>
</div>

<?= GridView::widget([
    'dataProvider' => $programDataProvider,
    'columns' => [
        'name',
        [
            'class' => ActionColumn::className(),
            'header' => 'Выбрать все<br>' . Html::checkbox('', false, ['id' => 'change-all-auto-prolongation', 'data' => ['url' => Url::to('change-auto-prolongation')]]),
            'template' => '{checkbox}',
            'buttons' => [
                'checkbox' => function ($url, $model, $key) {
                    /** @var $model Programs */
                    return Html::checkbox('', $model->auto_prolongation_enabled, [
                        'class' => 'change-auto-prolongation-checkbox',
                        'data' => [
                            'url' => Url::to(['change-auto-prolongation', 'id' => $model->id])
                        ]
                    ]);
                }
            ]
        ],
    ],
]); ?>
