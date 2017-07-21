<?php
use app\helpers\GridviewHelper;
use app\models\Mun;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchRequest \app\models\search\OrganizationSearch */
/* @var $searchRegistry \app\models\search\OrganizationSearch */
/* @var $registryProvider \yii\data\ActiveDataProvider */
/* @var $requestProvider \yii\data\ActiveDataProvider */
$this->title = 'Поставщики образовательных услуг';
$this->params['breadcrumbs'][] = $this->title;
?>

<ul class="nav nav-tabs">
    <li class="active">
        <a data-toggle="tab" href="#panel-registry">Реестр
            <span class="badge"><?= $registryProvider->totalCount ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel-requests">Заявки
            <span class="badge"><?= $requestProvider->totalCount ?></span>
        </a>
    </li>
</ul>
<br>
<div class="tab-content">
    <div id="panel-registry" class="tab-pane fade in active">
        <?php
        $registryColumns = [
            'name',
            'cratedate',
            'site',
            'phone',
            'max_child',
            'raiting',
            [
                'attribute' => 'type',
                'value' => function ($model) {
                /** @var \app\models\Organization $model */
                    return $model::types()[$model->type];
                },
                'type' => SearchFilter::TYPE_DROPDOWN,
                'data' => $searchRegistry::types(),
            ],
            [
                'attribute' => 'mun',
                'value' => 'municipality.name',
                'type' => SearchFilter::TYPE_DROPDOWN,
                'data' => ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
            ],
            [
                'attribute' => 'programs',
                'value' => function ($model) {
                    /** @var \app\models\Organization $model */
                    return $model->getPrograms()->andWhere(['programs.verification' => 2])->count();
                },
                'type' => SearchFilter::TYPE_RANGE_SLIDER,
            ],
            [
                'attribute' => 'children',
                'value' => function ($model) {
                    /** @var \app\models\Organization $model */
                    return count(array_unique(ArrayHelper::toArray(
                        $model->getChildren()->andWhere(['contracts.status' => 1])->all()
                    )));
                },
                'type' => SearchFilter::TYPE_RANGE_SLIDER,
            ],
            [
                'attribute' => 'amount_child',
                'type' => SearchFilter::TYPE_RANGE_SLIDER,
            ],
            [
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
            ],
            [
                'class' => ActionColumn::class,
                'controller' => 'organization',
                'template' => '{view}',
                'searchFilter' => false,
            ],
        ]
        ?>
        <?= SearchFilter::widget([
            'model' => $searchRegistry,
            'action' => ['personal/operator-organizations'],
            'data' => GridviewHelper::prepareColumns(
                'organizations',
                $registryColumns,
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'register'
        ]); ?>
        <?= Html::a(
            'Добавить поставщика образовательных услуг',
            ['organization/create'],
            ['class' => 'btn btn-success']
        ) ?>
        <div class="pull-right">
            <?= Html::a('Пересчитать лимиты', ['organization/alllimit'], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Пересчитать рейтинги', ['organization/allraiting'], ['class' => 'btn btn-primary']) ?>
        </div><br><br>
        <?php $preparedRegistryColumns = GridviewHelper::prepareColumns('organization', $registryColumns); ?>
        <?= GridView::widget([
            'dataProvider' => $registryProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedRegistryColumns,
        ]); ?>
        <?php array_pop($preparedRegistryColumns) ?>
        <?= ExportMenu::widget([
            'dataProvider' => $registryProvider,
            'target' => '_self',
            'exportConfig' => [
                ExportMenu::FORMAT_EXCEL => false,
            ],
            'columns' => $preparedRegistryColumns,
        ]); ?>
    </div>
    <div id="panel-requests" class="tab-pane fade">
        <?php
        $requestColumns = [
            'name',
            'fio_contact',
            'site',
            'email',
            [
                'attribute' => 'mun',
                'value' => 'municipality.name',
                'type' => SearchFilter::TYPE_DROPDOWN,
                'data' => ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
            ],
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    /** @var \app\models\Organization $model */
                    return $model::types()[$model->type];
                },
                'type' => SearchFilter::TYPE_DROPDOWN,
                'data' => $searchRequest::types()
            ],
            [
                'class' => ActionColumn::class,
                'controller' => 'organization',
                'template' => '{update}',
                'searchFilter' => false,
            ],
        ];
        ?>
        <?= SearchFilter::widget([
            'model' => $searchRequest,
            'action' => ['personal/operator-organizations'],
            'data' => GridviewHelper::prepareColumns(
                'organizations',
                $requestColumns,
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'request'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $requestProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'columns' =>  GridviewHelper::prepareColumns('organization', $requestColumns)
        ]); ?>
    </div>
</div>