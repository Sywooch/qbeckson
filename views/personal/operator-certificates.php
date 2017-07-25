<?php

use app\helpers\GridviewHelper;
use app\models\CertGroup;
use app\models\Mun;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'Сертификаты';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$columns = [
    [
        'attribute' => 'payerMunicipality',
        'value' => 'payer.municipality.name',
        'label' => 'Муниципалитет',
        'type' => SearchFilter::TYPE_DROPDOWN,
        'data' => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
    ],
    [
        'attribute' => 'number',
        'label' => 'Номер',
    ],
    [
        'attribute' => 'payer',
        'value' => function ($model) {
            return Html::a(
                $model->payer->name,
                Url::to(['payers/view', 'id' => $model->payer->id]),
                ['class' => 'blue', 'target' => '_blank']
            );
        },
        'format' => 'raw'
    ],
    [
        'attribute' => 'soname',
    ],
    [
        'attribute' => 'name',
    ],
    [
        'attribute' => 'phname',
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
    'action' => ['personal/operator-certificates'],
    'data' => GridviewHelper::prepareColumns(
        'certificates',
        $columns,
        null,
        'searchFilter',
        null
    ),
    'role' => UserIdentity::ROLE_OPERATOR,
]); ?>
<?php $preparedColumns = GridviewHelper::prepareColumns('certificates', $columns) ?>
<?= GridView::widget([
    'dataProvider' => $certificatesProvider,
    'filterModel' => null,
    'pjax' => true,
    'summary' => false,
    'columns' => $preparedColumns,
]); ?>
<?php array_pop($preparedColumns); ?>
<?= ExportMenu::widget([
    'dataProvider' => $certificatesProvider,
    'exportConfig' => [
        ExportMenu::FORMAT_EXCEL => false,
    ],
    'target' => '_self',
    'columns' => array_merge(['id', 'user_id'], $preparedColumns),
]); ?>
