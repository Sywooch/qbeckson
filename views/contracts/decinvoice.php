<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use yii\grid\GridView;
use app\models\Organization;
use app\models\Contracts;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvoicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = 'Счет будет выставлен по следующим договорам:';
$this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
$this->params['breadcrumbs'][] = ['label' => 'Полнота оказанных услуг в декабре', 'url' => ['/groups/dec']];
$this->params['breadcrumbs'][] = ['label' => 'Выберите плательщика', 'url' => ['/contracts/dec']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoices-index">

    <?php
    $organizations = new Organization();
    $organization = $organizations->getOrganization();


    $lmonth = Yii::$app->params['decemberNumber'];
    $start = date('Y') . '-' . $lmonth . '-01';
    $cal_days_in_month = cal_days_in_month(CAL_GREGORIAN, $lmonth, date('Y'));
    $stop = date('Y') . '-' . $lmonth . '-' . $cal_days_in_month;

    $contracts_all = (new \yii\db\Query())
        ->select(['id'])
        ->from('contracts')
        ->where(['<=', 'start_edu_contract', $stop])
        ->andWhere(['>=', 'stop_edu_contract', $start])
        ->andWhere(['organization_id' => $organization->id])
        ->andWhere(['payer_id' => $payers->payer_id])
        ->andWhere(['status' => Contracts::STATUS_ACTIVE])
        ->andWhere(['>', 'all_funds', 0])
        ->column();

    $contracts_terminated = (new \yii\db\Query())
        ->select(['id'])
        ->from('contracts')
        ->where(['<=', 'start_edu_contract', $stop])
        ->andWhere(['>=', 'stop_edu_contract', $start])
        ->andWhere(['organization_id' => $organization->id])
        ->andWhere(['payer_id' => $payers->payer_id])
        ->andWhere(['status' => Contracts::STATUS_CLOSED])
        ->andWhere(['<=', 'date_termnate', $stop])
        ->andWhere(['>=', 'date_termnate', $start])
        ->andWhere(['>', 'all_funds', 0])
        ->column();

    $contracts = array_merge($contracts_all, $contracts_terminated);

    $sum = 0;
    foreach ($contracts as $contract_id) {
        $contract = Contracts::findOne($contract_id);

        $completeness = (new \yii\db\Query())
            ->select(['sum'])
            ->from('completeness')
            ->where(['contract_id' => $contract->id])
            ->andWhere(['preinvoice' => 0])
            ->andWhere(['month' => $lmonth])
            ->one();

        $sum += $completeness['sum'];
    }

    echo '<h1>Всего необходимо для оплаты договоров - ' . round($sum, 2) . ' руб.</h1>';
    ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::beginForm(['invoices/dec', 'payer' => $payers['payer_id']], 'post'); ?>

    <?= GridView::widget([
        'options' => ['id' => 'invoices'],
        'dataProvider' => $ContractsProvider,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'number',
            'date',
            'certificate.number',
            [
                'label' => 'Процент',
                'value' => function ($model) {

                    $completeness = (new \yii\db\Query())
                        ->select(['completeness'])
                        ->from('completeness')
                        ->where(['contract_id' => $model->id])
                        ->andWhere(['month' => Yii::$app->params['decemberNumber']])
                        ->andWhere(['preinvoice' => 0])
                        ->one();

                    return $completeness['completeness'];
                }
            ],
            [
                'label' => 'К оплате',
                'value' => function ($model) {

                    $completeness = (new \yii\db\Query())
                        ->select(['sum'])
                        ->from('completeness')
                        ->where(['contract_id' => $model->id])
                        ->andWhere(['month' => Yii::$app->params['decemberNumber']])
                        ->andWhere(['preinvoice' => 0])
                        ->one();
                    return round($completeness['sum'], 2);
                }
            ],
        ],
    ]); ?>

    <?= Html::a('Назад', ['/contracts/dec'], ['class' => 'btn btn-primary']) ?>
    &nbsp;
    <?= Html::submitButton('Продолжить', ['class' => 'btn btn-primary',]); ?>

    <?= Html::endForm(); ?>
</div>
