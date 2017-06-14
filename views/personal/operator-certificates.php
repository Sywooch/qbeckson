<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use app\models\Contracts;

/* @var $this yii\web\View */

$this->title = 'Сертификаты';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
    $columns = [
        [
            'attribute' => 'number',
            'label' => 'Номер',
        ],
        'fio_child',
        [
            'attribute' => 'payers',
            'format' => 'html',
            'value' => function($data) {
                return Html::a($data->payers->name, Url::to(['/payers/view', 'id' => $data->payers->id]), ['class' => 'blue', 'target' => '_blank']);
            },
            'label' => 'Плательщик',
        ],
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
            'label' => 'Договоров',
            'value' => function($data) {
                return Contracts::getCountContracts(['certificateId' => $data->id]);
            }
        ],
        [
            'attribute' => 'actual',
            'value' => function($data) {
                return $data->actual == 0 ? '-' : '+';
            }
        ],
    ];
?>

<?= GridView::widget([
    'dataProvider' => $CertificatesProvider,
    'filterModel' => $searchCertificates,
    'pjax' => true,
    'summary' => false,
    'summary' => false,
    'columns' => array_merge($columns,
        [['class' => 'yii\grid\ActionColumn',
            'controller' => 'certificates',
            'template' => '{view}',
        ]]),
]); ?>

<?= ExportMenu::widget([
    'dataProvider' => $CertificatesExportProvider,
    'exportConfig' => [
        ExportMenu::FORMAT_EXCEL => false,
    ],
    'target' => '_self',
    'columns' => array_merge(['id', 'user_id'], $columns),
]); ?>
