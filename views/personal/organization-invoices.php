<?php

use app\helpers\AppHelper;
use app\helpers\GridviewHelper;
use app\models\Organization;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\db\Query;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Счета';
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $exposedSearchInvoices \app\models\search\InvoicesSearch */
/* @var $exposedInvoicesProvider \yii\data\ActiveDataProvider */
/* @var $paidSearchInvoices \app\models\search\InvoicesSearch */
/* @var $paidInvoicesProvider \yii\data\ActiveDataProvider */
/* @var $removedSearchInvoices \app\models\search\InvoicesSearch */
/* @var $removedInvoicesProvider \yii\data\ActiveDataProvider */

$columns = [
    [
        'attribute' => 'number'
    ],
    [
        'attribute' => 'date'
    ],
    [
        'attribute' => 'month',
        'label' => 'Месяц',
        'value' => function ($model) {
            /** @var \app\models\Invoices $model */
            return AppHelper::getMonthName($model->month);
        },
        'type' => SearchFilter::TYPE_DROPDOWN,
        'data' => AppHelper::monthes(),
    ],
    [
        'attribute' => 'payer',
        'label' => 'Организация',
        'format' => 'raw',
        'value' => function ($model) {
            /** @var \app\models\Invoices $model */
            return Html::a(
                $model->payer->name,
                Url::to(['payers/view', 'id' => $model->payer->id]),
                ['target' => '_blank', 'data-pjax' => '0']
            );
        },
    ],
    [
        'attribute' => 'prepayment',
        'label' => 'Тип',
        'format' => 'raw',
        'value' => function ($model) {
            /** @var \app\models\Invoices $model */
            return $model->prepayment === 1 ? 'Аванс' : 'Счёт';
        },
        'type' => SearchFilter::TYPE_DROPDOWN,
        'data' => [
            0 => 'Счёт',
            1 => 'Аванс',
        ],
    ],
    [
        'attribute' => 'status',
        'format' => 'raw',
        'value' => function ($model) {
            /** @var \app\models\Invoices $model */
            return $model::statuses()[$model->status];
        },
        'type' => SearchFilter::TYPE_DROPDOWN,
        'data' => $exposedSearchInvoices::statuses(),
    ],
    [
        'attribute' => 'sum',
        'type' => SearchFilter::TYPE_RANGE_SLIDER,
        'pluginOptions' => [
            'max' => 10000000
        ]
    ],
    [
        'attribute' => 'link',
        'label' => 'Скачать',
        'format' => 'raw',
        'value' => function ($model) {
            /** @var \app\models\Invoices $model */
            if ($model->prepayment == 1) {
                return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', !empty($model->pdf) ? Url::to('@pfdo' . $model->pdf) : Url::to(['invoices/mpdf', 'id' => $model->id]));
            }

            return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', !empty($model->pdf) ? Url::to('@pfdo' . $model->pdf) : Url::to(['invoices/invoice', 'id' => $model->id]));
        }
    ],
    [
        'class' => ActionColumn::class,
        'controller' => 'invoices',
        'template' => '{view}',
        'searchFilter' => false,
    ],
];

$preparedColumns = GridviewHelper::prepareColumns('invoices', $columns);
$month = date('m');
$year = date('Y');
?>

<?php
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
    $organizations = new Organization();
    $organization = $organizations->getOrganization();
    if ($roles['organizations'] and $organization['actual'] != 0) {
        
            $rows = (new Query())
                ->select(['payer_id'])
                ->from('cooperate')
                ->where(['organization_id' => $organization['id']])
                ->andWhere(['status' => 1])
                ->column();

            if (date('m') == Yii::$app->params['decemberNumber']) {
                $dec = array();
                foreach ($rows as $payer_id) {
                    $payer3 = (new Query())
                        ->select(['id'])
                        ->from('invoices')
                        ->where(['organization_id' => $organization['id']])
                        ->andWhere(['payers_id' => $payer_id])
                        ->andWhere(['month' => Yii::$app->params['decemberNumber'], 'year' => $year])
                        ->andWhere(['prepayment' => 0])
                        ->andWhere(['status' => [0,1,2]])
                        ->column();

                    if (!$payer3) {
                        array_push($dec, $payer_id);
                    }
                }
            }
            
            $preinvoice = array();
            foreach ($rows as $payer_id) {
                if($month != Yii::$app->params['decemberNumber'] || (isset($dec) && in_array($payer_id, $dec))) {
                    $payer = (new Query())
                        ->select(['id'])
                        ->from('invoices')
                        ->where(['organization_id' => $organization['id']])
                        ->andWhere(['payers_id' => $payer_id])
                        ->andWhere(['month' => date('m'), 'year' => $year])
                        ->andWhere(['prepayment' => 1])
                        ->andWhere(['status' => [0, 1, 2]])
                        ->column();

                    if (!$payer) {
                        array_push($preinvoice, $payer_id);
                    }
                }
            }
        
            $invoice = array();
            foreach ($rows as $payer_id) {
                $payer2 = (new Query())
                    ->select(['id'])
                    ->from('invoices')
                    ->where(['organization_id' => $organization['id']])
                    ->andWhere(['payers_id' => $payer_id])
                    ->andWhere(['month' => date('m')-1, 'year' => $year])
                    ->andWhere(['prepayment' => 0])
                    ->andWhere(['status' => [0,1,2]])
                    ->column();
                
                if (!$payer2) {
                    array_push($invoice, $payer_id);
                }
            }

        $month_last = \app\helpers\AppHelper::getMonthName(date('n', strtotime('first day of previous month')));

        $month_now = \app\helpers\AppHelper::getMonthName(date('n'));

        echo "<p>";
        if ($invoice && date('m') != 1) {
            echo Html::a('Создать счет за ' . mb_strtolower($month_last), ['groups/invoice'], ['class' => 'btn btn-success']);
        }
        if ($preinvoice) {
            echo "&nbsp;";
            echo Html::a('Создать аванс за ' . mb_strtolower($month_now), ['groups/preinvoice'], ['class' => 'btn btn-success']);
        }
        if (!empty($dec)) {
            echo Html::a('Создать счет за декабрь', ['groups/dec'], ['class' => 'btn btn-warning pull-right']);
            echo "<br>";
            echo "<br>";
        }
        echo "</p>";
    }
    ?>

<ul class="nav nav-tabs">
    <li class="active">
        <a data-toggle="tab" href="#panel1">Выставленные
            <span class="badge"><?= $exposedInvoicesProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel2">Оплаченные
            <span class="badge"><?= $paidInvoicesProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel3">Удаленные
            <span class="badge"><?= $removedInvoicesProvider->getTotalCount() ?></span>
        </a>
    </li>
</ul>

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <?php
        echo SearchFilter::widget([
            'model' => $exposedSearchInvoices,
            'action' => ['personal/organization-invoices'],
            'data' => GridviewHelper::prepareColumns(
                'invoices',
                $columns,
                null,
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
        ]);

        echo GridView::widget([
            'dataProvider' => $exposedInvoicesProvider,
            'filterModel' => null,
            'columns' => $preparedColumns
        ]);
        ?>
    </div>

    <div id="panel2" class="tab-pane fade">
        <?php
        echo SearchFilter::widget([
            'model' => $paidSearchInvoices,
            'action' => ['personal/organization-invoices'],
            'data' => GridviewHelper::prepareColumns(
                'invoices',
                $columns,
                null,
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
        ]);

        echo GridView::widget([
            'dataProvider' => $paidInvoicesProvider,
            'filterModel' => null,
            'columns' => $preparedColumns
        ]);
        ?>
    </div>

    <div id="panel3" class="tab-pane fade">
        <?php
        echo SearchFilter::widget([
            'model' => $removedSearchInvoices,
            'action' => ['personal/organization-invoices'],
            'data' => GridviewHelper::prepareColumns(
                'invoices',
                $columns,
                null,
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_ORGANIZATION,
        ]);

        echo GridView::widget([
            'dataProvider' => $removedInvoicesProvider,
            'filterModel' => null,
            'columns' => $preparedColumns
        ]);
        ?>
    </div>
</div>
