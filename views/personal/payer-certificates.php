<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use app\widgets\SearchFilter;
use app\helpers\GridviewHelper;
use app\helpers\PermissionHelper;

$this->title = 'Сертификаты';
$this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */

$columns = [
    [
        'attribute' => 'number',
        'label' => 'Номер',
    ],
    [
        'attribute' => 'fio_child',
    ],
    [
        'attribute' => 'nominal',
        'type' => SearchFilter::TYPE_RANGE_SLIDER,
    ],
    [
        'attribute' => 'rezerv',
        'label' => 'Резерв',
        'type' => SearchFilter::TYPE_RANGE_SLIDER,
        'value' => function ($data) {
            return abs(round($data->rezerv));
        },
    ],
    [
        'attribute' => 'balance',
        'label' => 'Остаток',
        'type' => SearchFilter::TYPE_RANGE_SLIDER,
        'value' => function ($data) {
            return round($data->balance);
        },
    ],
    [
        'attribute' => 'contractCount',
        'label' => 'Договоров',
        'type' => SearchFilter::TYPE_TOUCH_SPIN,
    ],
    [
        'attribute' => 'cert_group',
        'value' => 'certGroup.group',
        'type' => SearchFilter::TYPE_SELECT2,
    ],
    [
        'attribute' => 'actual',
        'value' => function ($data) {
            return $data->actual > 0 ? '+' : '-';
        },
        'type' => SearchFilter::TYPE_DROPDOWN,
        'data' => [1 => 'Активен', 0 => 'Приостановлен'],
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'controller' => 'certificates',
        'template' => '{view}',
        'searchFilter' => false,
    ],
];
?>

<?php echo SearchFilter::widget([
    'model' => $searchCertificates,
    'action' => ['personal/payer-certificates'],
    'data' => GridviewHelper::prepareColumns('certificates', $columns, 'searchFilter', null),
]); ?>

<?php if (PermissionHelper::checkMonitorUrl('/certificates/allnominal')): ?>
<div class="pull-right">
    <?= Html::a('Обновить номиналы', Url::to(['/certificates/allnominal', 'id' => $payer_id]), ['class' => 'btn btn-success']) ?>
</div>
<?php endif; ?>

<p>
    <?php if (PermissionHelper::checkMonitorUrl('/certificates/create')): ?>
    <?= Html::a('Добавить сертификат', ['/certificates/create'], ['class' => 'btn btn-success']) ?>
    <?php elseif (PermissionHelper::checkMonitorUrl('/certificates/allnominal')): ?>
        <br />
        <br />
    <?php endif; ?>
</p>

<?= GridView::widget([
    'dataProvider' => $certificatesProvider,
    'filterModel' => null,
    'pjax' => true,
    'summary' => false,
    'columns' => GridviewHelper::prepareColumns('certificates', $columns),
]); ?>
