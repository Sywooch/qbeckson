<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;


$this->title = 'Сертификаты';
$this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */
?>

<?php echo $this->render('/certificates/_search', [
    'model' => $searchCertificates,
    'action' => ['personal/payer-certificates'],
]); ?>

<div class="pull-right">
    <?= Html::a('Обновить номиналы', Url::to(['/certificates/allnominal', 'id' => $payer_id]), ['class' => 'btn btn-success']) ?>
    <a href="javascrip:void(0);" class="btn btn-warning show-search-form">Расширенный поиск</a>
</div>

<p>
    <?= Html::a('Добавить сертификат', ['certificates/create'], ['class' => 'btn btn-success']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $certificatesProvider,
    'filterModel' => null,
    'pjax' => true,
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
            'attribute' => 'contractCount',
            'label' => 'Договоров',
        ],
        [
            'attribute' => 'actual',
            'value' => function ($data) {
                return $data->actual > 0 ? '+' : '-';
            }
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'certificates',
            'template' => '{view}',
        ],
    ],
]); ?>
