<?php
use app\helpers\GridviewHelper;
use app\models\Organization;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use yii\db\Query;
use yii\grid\ActionColumn;
use app\helpers\AppHelper;
use yii\helpers\Html;
use yii\grid\GridView;
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
                ['class' => 'blue', 'target' => '_blank']
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
    ],
    [
        'attribute' => 'link',
        'label' => 'Скачать',
        'format' => 'raw',
        'value' => function ($model) {
            /** @var \app\models\Invoices $model */
            if ($model->prepayment === 1) {
                return Html::a(
                    '<span class="glyphicon glyphicon-download-alt"></span>',
                    Url::to(['invoices/mpdf', 'id' => $model->id])
                );
            }

            return Html::a(
                '<span class="glyphicon glyphicon-download-alt"></span>',
                Url::to(['invoices/invoice', 'id' => $model->id])
            );
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

        
        $date_last=explode(".", date("d.m.Y"));
            switch ($date_last[1] - 1){
            case 1: $m='январь'; break;
            case 2: $m='февраль'; break;
            case 3: $m='март'; break;
            case 4: $m='апрель'; break;
            case 5: $m='май'; break;
            case 6: $m='июнь'; break;
            case 7: $m='июль'; break;
            case 8: $m='август'; break;
            case 9: $m='сентябрь'; break;
            case 10: $m='октябрь'; break;
            case 11: $m='ноябрь'; break;
            case 12: $m='декабрь'; break;
            }
        
        $date_now=explode(".", date("d.m.Y"));
            switch ($date_now[1]){
            case 1: $m_now='январь'; break;
            case 2: $m_now='февраль'; break;
            case 3: $m_now='март'; break;
            case 4: $m_now='апрель'; break;
            case 5: $m_now='май'; break;
            case 6: $m_now='июнь'; break;
            case 7: $m_now='июль'; break;
            case 8: $m_now='август'; break;
            case 9: $m_now='сентябрь'; break;
            case 10: $m_now='октябрь'; break;
            case 11: $m_now='ноябрь'; break;
            case 12: $m_now='декабрь'; break;
            }
            
        
        echo "<p>";
        if ($invoice && date('m') != 1) {
            echo Html::a('Создать счет за '.$m , ['groups/invoice'], ['class' => 'btn btn-success']);
        }
        if ($preinvoice) {
            echo "&nbsp;";
            echo Html::a('Создать аванс за '.$m_now , ['groups/preinvoice'], ['class' => 'btn btn-success']);
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
