<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;


$this->title = 'Сертификаты';
$this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */
?>

<div class="pull-right">
    <?= Html::a('Обновить номиналы', Url::to(['/certificates/allnominal', 'id' => $payer_id]), ['class' => 'btn btn-success']) ?>
</div>

<p>
    <?= Html::a('Добавить сертификат', ['certificates/create'], ['class' => 'btn btn-success']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $certificatesProvider,
    'filterModel' => $searchCertificates,
    'pjax' => true,
    'summary' => false,
    'summary' => false,
    'columns' => [
        [
            'attribute' => 'number',
            'label' => 'Номер',
        ],
        'fio_child',
        [
            'attribute' => 'nominal',
            'label' => 'Номинал',
        ],
        [
            'attribute' => 'rezerv',
            'label' => 'Резерв',
        ],
        [
            'attribute' => 'balance',
            'label' => 'Остаток',
        ],
        [
            'attribute' => 'balance',
            'label' => 'Остаток',
        ],
        [
            'attribute' => 'contractCount',
            'label' => 'Договоров',
        ],
        [
            'attribute' => 'actual',
            'value' => function ($data) {
                return $data->actual > 0 ? '+' : '-';
            }
        ],

        ['class' => 'yii\grid\ActionColumn',
            'controller' => 'certificates',
            'template' => '{view}',
        ],
    ],
]); ?>
