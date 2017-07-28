<?php

use app\helpers\GridviewHelper;
use app\models\Mun;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchCertificates \app\models\search\CertificatesSearch */
/* @var $certificatesProvider \yii\data\ActiveDataProvider */
/* @var $allCertificatesProvider \yii\data\ActiveDataProvider */

$this->title = 'Сертификаты';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$columns = [
    [
        'attribute' => 'payer_id',
        'type' => SearchFilter::TYPE_HIDDEN
    ],
    [
        'attribute' => 'payerMunicipality',
        'value' => function ($model) {
            /** @var \app\models\Certificates $model */
            return Html::a(
                $model->payer->municipality->name,
                ['mun/view', 'id' => $model->payer->municipality->id],
                ['target' => '_blank', 'data-pjax' => '0']
            );
        },
        'label' => 'Муниципалитет',
        'format' => 'raw',
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
<?= SearchFilter::widget([
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
    'columns' => $preparedColumns,
]); ?>
<p class="lead">Экспорт данных:</p>
<?= ExportMenu::widget([
    'dataProvider' => $allCertificatesProvider,
    'exportConfig' => [
        ExportMenu::FORMAT_EXCEL => false,
    ],
    'target' => ExportMenu::TARGET_BLANK,
    'showColumnSelector' => false,
    'columns' => GridviewHelper::prepareExportColumns($columns),
]); ?>
<br>
<br>
<p class=""><strong><span class="warning">*</span> Загрузка начнётся в новом окне и может занять некоторое время.</strong></p>
