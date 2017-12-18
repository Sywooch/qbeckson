<?php

use app\helpers\GridviewHelper;
use app\helpers\PermissionHelper;
use app\models\CertGroup;
use app\models\certificates\CertificateToAccountingConfirmForm;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Сертификаты';
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $searchCertificates \app\models\search\CertificatesSearch */
/* @var $certificatesProviderAccounting \yii\data\ActiveDataProvider */
/* @var $certificatesProviderPf \yii\data\ActiveDataProvider */
/* @var $allCertificatesProvider \yii\data\ActiveDataProvider */
/* @var $certificateToAccountingConfirmForm CertificateToAccountingConfirmForm */
/* @var $certificateImportTemplateExists boolean */

$columns = [
    [
        'attribute' => 'number',
        'label' => 'Номер',
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
        'attribute' => 'cert_group',
        'value' => 'certGroup.group',
        'type' => SearchFilter::TYPE_SELECT2,
        'data' => ArrayHelper::map(
            CertGroup::findAll(['payer_id' => Yii::$app->user->getIdentity()->payer->id]),
            'id',
            'group'
        ),
    ],
    [
        'class' => ActionColumn::class,
        'controller' => 'certificates',
        'template' => '{view}',
        'searchFilter' => false,
    ],
];
?>
<?= SearchFilter::widget([
    'model' => $searchCertificates,
    'action' => ['personal/payer-certificates'],
    'data' => GridviewHelper::prepareColumns(
        'certificates',
        $columns,
        null,
        'searchFilter',
        null
    ),
    'role' => UserIdentity::ROLE_PAYER
]); ?>

<div class="row">
    <div class="col-xs-6">
        <?php if (PermissionHelper::checkMonitorUrl('/certificates/create')) : ?>
            <?= Html::a('Добавить один сертификат', ['/certificates/create'], ['class' => 'btn btn-success']) ?>
        <?php elseif (PermissionHelper::checkMonitorUrl('/certificates/allnominal')) : ?>
            <br>
            <br>
        <?php endif; ?>

        <?php if ($certificateImportTemplateExists): ?>
            <?= Html::a('Импорт списка сертификатов', ['certificates/certificate-import'], ['class' => 'btn btn-success inline']) ?>
        <?php endif; ?>
    </div>

    <div class="col-xs-6">
        <?php Modal::begin([
            'header' => 'Подтверждение перевода неиспользуемых сертификатов в сертификаты учета',
            'id' => 'certificate-change-type-confirmation-modal',
            'toggleButton' => [
                'label' => 'Перевести неиспользуемые сертификаты в сертификаты учета',
                'class' => 'btn btn-danger pull-right'
            ],
        ]) ?>

        <p>В соответствии с регламентом проведения оценки использования сертификатов дополнительного образования неиспользуемые в течении <?= Yii::$app->user->identity->payer->days_to_first_contract_request ?>
            дней сертификаты подлежат переводу в сертификаты учета</p>

        <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => true,
            'options' => [
                'data-pjax' => true
            ]
        ]) ?>

        <?= $form->field($certificateToAccountingConfirmForm, 'changeTypeConfirmed')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('выполнить', ['class' => 'btn btn-danger']) ?>
        </div>
        <?php $form->end() ?>

        <?php Modal::end() ?>
    </div>
</div>
<?php
$preparedColumns = GridviewHelper::prepareColumns('certificates', $columns);
$items = [
    [
        'label' => 'Сертификаты ПФ',
        'content' => GridView::widget([
            'dataProvider' => $certificatesProviderPf,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedColumns,
        ]),
        'active' => true
    ],
    [
        'label' => 'Сертификаты учета',
        'content' => GridView::widget([
            'dataProvider' => $certificatesProviderAccounting,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedColumns,
        ])
    ],

];

echo Tabs::widget([
    'items' => $items
]);
?>

<?= \app\widgets\Export::widget([
    'dataProvider' => $allCertificatesProvider,
    'columns' => GridviewHelper::prepareColumns('certificates', $columns, null, 'export'),
    'group' => 'payer-certificates',
    'table' => 'certificates',
]); ?>
