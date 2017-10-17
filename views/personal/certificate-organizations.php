<?php

use app\helpers\GridviewHelper;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use kartik\grid\GridView;
use app\models\Certificates;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $organizationProvider \yii\data\ActiveDataProvider */
/* @var $searchOrganization \app\models\search\OrganizationSearch */
$this->title = 'Организации';
$this->params['breadcrumbs'][] = 'Организации';

$name = [
    'attribute' => 'name',
];
$type = [
    'attribute' => 'type',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        return $model::types()[$model->type];
    },
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => $searchOrganization::types(),
];
$programs = [
    'attribute' => 'programs',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        $programsCount = $model->getPrograms()->andWhere(['programs.verification' => 2])->count();
        return (int)$programsCount > 0 ? $programsCount : '-';
    },
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 1000
    ]
];
$children = [
    'attribute' => 'children',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        $childrenCount = $model->getChildren()->select('certificates.id')->distinct()->leftJoin(Certificates::tableName(), 'certificates.id = contracts.certificate_id')->andWhere(['contracts.status' => 1])->count();
        return $childrenCount > 0 ? $childrenCount : '-';
    },
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 10000
    ]
];
$max_child = [
    'attribute' => 'max_child',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 10000
    ]
];
$raiting = [
    'attribute' => 'raiting',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 100
    ]
];
$actual = [
    'attribute' => 'actual',
    'value' => function ($model) {
        /** @var \app\models\Organization $model */
        return $model->actual === 0 ? '-' : '+';
    },
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => [
        1 => 'Да',
        0 => 'Нет'
    ]
];
$actions = [
    'class' => ActionColumn::class,
    'controller' => 'organization',
    'template' => '{view}',
    'searchFilter' => false,
];

$columns = [
    $name,
    $type,
    $programs,
    $children,
    $max_child,
    $raiting,
    $actual,
    $actions,
];
?>
<?php if (Yii::$app->user->can('certificate')) : ?>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= $this->render('../common/_select-municipality-modal') ?>
            </div>
        </div>
    </div>
    <br>
<?php endif; ?>
<?= SearchFilter::widget([
    'model' => $searchOrganization,
    'action' => ['personal/certificate-organizations'],
    'data' => GridviewHelper::prepareColumns(
        'organization',
        $columns,
        null,
        'searchFilter',
        null
    ),
    'role' => UserIdentity::ROLE_CERTIFICATE,
]); ?>
<?= GridView::widget([
    'dataProvider' => $organizationProvider,
    'pjax' => true,
    'rowOptions' => function ($model) {
        if ($model) {
            $certificates = new Certificates();
            $certificate = $certificates->getCertificates();
            $rows = (new \yii\db\Query())
                ->select(['id'])
                ->from('cooperate')
                ->where(['payer_id' => $certificate['payer_id']])
                ->andWhere(['organization_id' => $model['id']])
                ->andWhere(['status' => 1])
                ->count();
            if ((int)$rows === 0) {
                return ['class' => 'danger'];
            }
        }
    },
    'columns' => GridviewHelper::prepareColumns('organization', $columns),
]); ?>

