<?php

use app\components\halpers\DeclinationOfMonths;
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
/* @var $searchInvoices \app\models\search\InvoicesSearch */
/* @var $invoicesProvider \yii\data\ActiveDataProvider */

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
        'data' => $searchInvoices::statuses(),
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
            if ($model->prepayment === 1) {
                return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', !empty($model->pdf) ? Url::to($model->pdf) : Url::to(['invoices/mpdf', 'id' => $model->id]));
            }

            return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', !empty($model->pdf) ? Url::to($model->pdf) : Url::to(['invoices/invoice', 'id' => $model->id]));
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
            
            $preinvoice = array();
            foreach ($rows as $payer_id) {
                $payer = (new Query())
                    ->select(['id'])
                    ->from('invoices')
                    ->where(['organization_id' => $organization['id']])
                    ->andWhere(['payers_id' => $payer_id])
                    ->andWhere(['month' => date('m')])
                    ->andWhere(['prepayment' => 1])
                    ->andWhere(['status' => [0,1,2]])
                    ->column();
                
                if (!$payer) {
                    array_push($preinvoice, $payer_id);
                }
            }
        
            $invoice = array();
            foreach ($rows as $payer_id) {
                $payer2 = (new Query())
                    ->select(['id'])
                    ->from('invoices')
                    ->where(['organization_id' => $organization['id']])
                    ->andWhere(['payers_id' => $payer_id])
                    ->andWhere(['month' => date('m')-1])
                    ->andWhere(['prepayment' => 0])
                    ->andWhere(['status' => [0,1,2]])
                    ->column();
                
                if (!$payer2) {
                    array_push($invoice, $payer_id);
                }
            }
        
            if (date('m') == 12) { 
            $dec = array();
            foreach ($rows as $payer_id) {
                $payer3 = (new Query())
                    ->select(['id'])
                    ->from('invoices')
                    ->where(['organization_id' => $organization['id']])
                    ->andWhere(['payers_id' => $payer_id])
                    ->andWhere(['month' => 12])
                    ->andWhere(['prepayment' => 0])
                    ->andWhere(['status' => [0,1,2]])
                    ->column();
                
                if (!$payer3) {
                    array_push($dec, $payer_id);
                }
            }
            }

        $month_last = DeclinationOfMonths::getMonthNameByNumberAsNominative(
            (int)(new DateTime())->modify('previous month')->format('m')
        );

        $month_now = DeclinationOfMonths::getMonthNameByNumberAsNominative(
            (int)(new DateTime())->format('m')
        );

            
        
        echo "<p>";
        if ($invoice && date('m') != 1) {
            echo Html::a('Создать счет за ' . $month_last, ['groups/invoice'], ['class' => 'btn btn-success']);
        }
        if ($preinvoice) {
            echo "&nbsp;";
            echo Html::a('Создать аванс за ' . $month_now, ['groups/preinvoice'], ['class' => 'btn btn-success']);
        }
        if (!empty($dec)) {
            echo Html::a('Создать счет за декабрь', ['groups/dec'], ['class' => 'btn btn-warning pull-right']);
            echo "<br>";
            echo "<br>";
        }
        echo "</p>";
    }
    ?>

<?php
echo SearchFilter::widget([
    'model' => $searchInvoices,
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
    'dataProvider' => $invoicesProvider,
    'filterModel' => null,
    'columns' => $preparedColumns
]);
?>
