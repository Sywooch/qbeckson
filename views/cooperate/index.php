<?php

use app\helpers\GridviewHelper;
use app\models\Mun;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CooperateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Соглашения';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    [
        'attribute' => 'number',
    ],
    [
        'attribute' => 'date',
        'format' => 'date',
        'label' => 'Дата соглашения',
    ],
    [
        'attribute' => 'organizationName',
        'label' => 'Организация',
        'format' => 'raw',
        'value' => function ($model) {
            /** @var \app\models\Cooperate $model */
            return Html::a(
                $model->organization->name,
                ['organization/view', 'id' => $model->organization->id],
                ['class' => 'blue', 'target' => '_blank']
            );
        },
    ],
    [
        'attribute' => 'payerName',
        'label' => 'Плательщик',
        'format' => 'raw',
        'value' => function ($model) {
            /** @var \app\models\Cooperate $model */
            return Html::a(
                $model->payer->name,
                ['payers/view', 'id' => $model->payer->id],
                ['class' => 'blue', 'target' => '_blank']
            );
        },
        'label'=> 'Плательщик',
    ],
    [
        'attribute' => 'payerMunicipality',
        'label' => 'Муниципалитет',
        'format' => 'raw',
        'data' => ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'),
        'type' => SearchFilter::TYPE_DROPDOWN,
        'value' => function ($model) {
            /** @var \app\models\Cooperate $model */
            return Html::a(
                $model->payer->municipality->name,
                ['mun/view', 'id' => $model->payer->municipality->id],
                ['class' => 'blue', 'target' => '_blank']
            );
        },
    ],
    [
        'label' => 'Число договоров',
        'attribute' => 'contractsCount',
        'format'=> 'raw',
        'value' => function ($data) {
            $contracts = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['payer_id' => $data->payers->id])
                ->andWhere(['organization_id' => $data->organization->id])
                ->count();

            return Html::a(
                $contracts,
                [
                    'personal/operator-contracts',
                    'SearchActiveContracts[organizationName]' => $data->organization->name,
                    'SearchActiveContracts[payerName]' => $data->payers->name
                ],
                ['class' => 'blue', 'target' => '_blank', 'data-pjax' => '0']
            );
        },
    ],
];

?>
<div class="cooperate-index">
    <?= SearchFilter::widget([
        'model' => $searchModel,
        'action' => ['cooperate/index'],
        'data' => GridviewHelper::prepareColumns(
            'cooperate',
            $columns,
            null,
            'searchFilter',
            null
        ),
        'role' => UserIdentity::ROLE_OPERATOR,
    ]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'columns' => GridviewHelper::prepareColumns('cooperate', $columns),
    ]); ?>
</div>
