<?php

use app\models\Contracts;
use app\models\UserIdentity;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvoicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$date = explode(".", date("d.m.Y"));
switch ($date[1] - 1) {
    case 1:
        $m = 'январе';
        break;
    case 2:
        $m = 'феврале';
        break;
    case 3:
        $m = 'марте';
        break;
    case 4:
        $m = 'апреле';
        break;
    case 5:
        $m = 'мае';
        break;
    case 6:
        $m = 'июне';
        break;
    case 7:
        $m = 'июле';
        break;
    case 8:
        $m = 'августе';
        break;
    case 9:
        $m = 'сентябре';
        break;
    case 10:
        $m = 'октябре';
        break;
    case 11:
        $m = 'ноябре';
        break;
    case 12:
        $m = 'декабре';
        break;
}

$this->title = 'Счет будет выставлен по следующим договорам:';
$this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
$this->params['breadcrumbs'][] = ['label' => 'Полнота оказанных услуг в ' . $m, 'url' => ['/groups/invoice']];
$this->params['breadcrumbs'][] = ['label' => 'Выберите плательщика', 'url' => ['/contracts/invoice']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoices-index">

    <?php
    /** @var $identity UserIdentity */
    $identity = Yii::$app->user->identity;
    $organization = $identity->organization;

    $lmonth = date('m') - 1;
    $start = date('Y') . '-' . $lmonth . '-01';

    $cal_days_in_month = cal_days_in_month(CAL_GREGORIAN, $lmonth, date('Y'));

    $stop = date('Y') . '-' . $lmonth . '-' . $cal_days_in_month;

    //return var_dump($payer);
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

    <?= Html::beginForm(['invoices/new', 'payer' => $payers['payer_id']], 'post'); ?>

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
                        ->andWhere(['month' => date('m') - 1])
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
                        ->andWhere(['month' => date('m') - 1])
                        ->andWhere(['preinvoice' => 0])
                        ->one();

                    return round($completeness['sum'], 2);
                }
            ],
        ],
    ]); ?>

    <?= Html::a('Назад', ['/contracts/invoice'], ['class' => 'btn btn-primary']) ?>
    &nbsp;
    <?= Html::submitButton('Продолжить', ['class' => 'btn btn-primary',]); ?>

    <?= Html::endForm(); ?>
</div>
