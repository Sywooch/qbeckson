<?php

use yii\grid\ActionColumn;
use yii\helpers\Html;
use app\helpers\GridviewHelper;
use app\models\Mun;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use app\models\Organization;

$this->title = 'Плательщики';
$this->params['breadcrumbs'][] = 'Плательщики';
/* @var $this yii\web\View */
/* @var $searchOpenPayers \app\models\PayersSearch */
/* @var $openPayersProvider \yii\data\ActiveDataProvider */
/* @var $searchWaitPayers \app\models\PayersSearch */
/* @var $waitPayersProvider \yii\data\ActiveDataProvider */
/* @var $searchRejectPayers \app\models\PayersSearch */
/* @var $rejectPayersProvider \yii\data\ActiveDataProvider */
/* @var $searchConfirmPayers \app\models\PayersSearch */
/* @var $confirmPayersProvider \yii\data\ActiveDataProvider */

$name = [
    'attribute' => 'name',
];
$phone = [
    'attribute' => 'phone',
];
$email = [
    'attribute' => 'email',
];
$fio = [
    'attribute' => 'fio',
];
$directionality = [
    'attribute' => 'directionality',
];
$mun = [
    'attribute' => 'mun',
    'value' => 'municipality.name',
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
];
$cooperates = [
    'attribute' => 'cooperates',
    'value' => function ($model) {
        /** @var \app\models\Payers $model */
        return $model->getCooperates()->andWhere(['status' => 1])->count();
    },
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
    'pluginOptions' => [
        'max' => 100
    ]
];
$certificates = [
    'attribute' => 'certificates',
    'value' => function ($model) {
        /** @var \app\models\Payers $model */
        return $model->getCertificates()->count();
    },
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
];
$actions = [
    'class' => ActionColumn::class,
    'controller' => 'payers',
    'template' => '{view}',
    'searchFilter' => false,
];

$columns = [
    $name,
    $phone,
    $email,
    $fio,
    $directionality,
    $mun,
    $cooperates,
    $certificates,
    $actions
];

$preparedColumns = GridviewHelper::prepareColumns('payers', $columns);

?>
<ul class="nav nav-tabs">
    <li class="active">
        <a data-toggle="tab" href="#panel1">Действующие
            <span class="badge"><?= $openPayersProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel2">Подтверждённые
            <span class="badge"><?= $confirmPayersProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel3">Ожидается подтверждение
            <span class="badge"><?= $waitPayersProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel4">Отклонены
            <span class="badge"><?= $rejectPayersProvider->getTotalCount() ?></span>
        </a>
    </li>
</ul>
<br>
<?php
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
$organizations = new Organization();
$organization = $organizations->getOrganization();
if ($roles['organizations'] and $organization['actual'] !== 0) {
    echo '<p>';
    echo Html::a('Зарегистрировать новое соглашение', ['payers/index'], ['class' => 'btn btn-success']);
    echo '</p>';
}
?>
<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <?= SearchFilter::widget([
            'model' => $searchOpenPayers,
            'action' => ['personal/organization-payers#panel1'],
            'data' => GridviewHelper::prepareColumns(
                'payers',
                $columns,
                null,
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $openPayersProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedColumns,
        ]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchConfirmPayers,
            'action' => ['personal/organization-payers#panel2'],
            'data' => GridviewHelper::prepareColumns(
                'payers',
                $columns,
                null,
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $confirmPayersProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedColumns,
        ]); ?>
    </div>
    <div id="panel3" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchWaitPayers,
            'action' => ['personal/organization-payers#panel3'],
            'data' => GridviewHelper::prepareColumns(
                'payers',
                $columns,
                null,
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $waitPayersProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedColumns,
        ]); ?>
    </div>
    <div id="panel4" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchRejectPayers,
            'action' => ['personal/organization-payers#panel4'],
            'data' => GridviewHelper::prepareColumns(
                'payers',
                $columns,
                null,
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $rejectPayersProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedColumns,
        ]); ?>
    </div>
</div>
