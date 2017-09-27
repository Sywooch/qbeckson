<?php

use app\models\Contracts;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Invoices */

$dateinvoice = explode('-', $model->date);
$this->title = '№ ' . $model->number . ' от ' . $dateinvoice[2] . '.' . $dateinvoice[1] . '.' . $dateinvoice[0];
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);

if (isset($roles['organizations'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
}
if (isset($roles['payer'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/payer-invoices']];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoices-view col-md-8 col-md-offset-2">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php
    if ($model->prepayment == 0) {
        $month = 'Месяц, за который выставлен счет';
        $sum = 'Сумма счета';
        $number = 'Номер счета';
        $date = 'Дата счета';
        $link = '/invoices/invoice';
        $inv = 'счет';
    } else {
        $month = 'Месяц, за который выставлен аванс';
        $sum = 'Сумма аванса';
        $number = 'Номер аванса';
        $date = 'Дата аванса';
        $link = '/invoices/mpdf';
        $inv = 'аванс';
    }

    switch ($model->month) {
        case 1:
            $m = 'январь';
            break;
        case 2:
            $m = 'февраль';
            break;
        case 3:
            $m = 'март';
            break;
        case 4:
            $m = 'апрель';
            break;
        case 5:
            $m = 'май';
            break;
        case 6:
            $m = 'июнь';
            break;
        case 7:
            $m = 'июль';
            break;
        case 8:
            $m = 'август';
            break;
        case 9:
            $m = 'сентябрь';
            break;
        case 10:
            $m = 'октябрь';
            break;
        case 11:
            $m = 'ноябрь';
            break;
        case 12:
            $m = 'декабрь';
            break;
    }

    ?>
    <?php
    if (isset($roles['organizations'])) {
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                //'id',
                [
                    'value' => $m,
                    'label' => $month,
                ],
                [
                    'label' => 'Плательщик',
                    'format' => 'raw',
                    'value' => Html::a($model->payers->name, Url::to(['/payers/view', 'id' => $model->payers->id]), ['class' => 'blue', 'target' => '_blank']),
                ],
                [
                    'attribute' => 'sum',
                    'label' => $sum,
                ],
                [
                    'attribute' => 'number',
                    'label' => $number,
                ],
                [
                    'attribute' => 'date',
                    'label' => $date,
                    'format' => 'date',
                ],
                [
                    'attribute' => 'link',
                    'format' => 'raw',
                    'value' => Html::a('<span class="glyphicon glyphicon-download-alt"></span>', !empty($model->pdf) ? Url::to($model->pdf) : Url::to([$link, 'id' => $model->id])),

                ],
                /*[
                    'attribute'=>'prepayment',
                    'format' => 'raw',
                    'value' => $model->prepayment == 1 ? 'Да' : 'Нет',

                ], */
                //'contracts',
            ],
        ]);
    }
    if (isset($roles['payer'])) {
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'value' => $m,
                    'label' => $month,
                ],
                [
                    'label' => 'Организация',
                    'format' => 'raw',
                    'value' => Html::a($model->organization->name, Url::to(['/organization/view', 'id' => $model->organization->id]), ['class' => 'blue', 'target' => '_blank']),
                ],
                [
                    'attribute' => 'sum',
                    'label' => $sum,
                ],
                [
                    'attribute' => 'number',
                    'label' => $number,
                ],
                [
                    'attribute' => 'date',
                    'label' => $date,
                    'format' => 'date',
                ],
                [
                    'attribute' => 'link',
                    'format' => 'raw',
                    'value' => Html::a('<span class="glyphicon glyphicon-download-alt"></span>', !empty($model->pdf) ? Url::to($model->pdf) : Url::to([$link, 'id' => $model->id])),

                ],
                /*[
                    'attribute'=>'prepayment',
                    'format' => 'raw',
                    'value' => $model->prepayment == 1 ? 'Да' : 'Нет',

                ], */
                //'contracts',
            ],
        ]);
    }
    ?>

    <?php
    $contracts = explode(",", $model->contracts);


    //  foreach ($contracts as $contract) {
    //$contr = Contracts::findOne($contract);

    $ContractsProvider = new ActiveDataProvider([
        'query' => Contracts::find()->where(['id' => $contracts]),
    ]);

    if ($ContractsProvider) {
        //return var_dump($contr);

        if ($model->prepayment == 0) {
            if ($model->month == 12) {
                echo GridView::widget([
                    'dataProvider' => $ContractsProvider,
                    'summary' => false,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'number',
                            'format' => 'raw',
                            'value' => function ($data) {
                                return Html::a($data->number, Url::to(['/contracts/view', 'id' => $data->id]));
                            }
                        ],
                        'date:date',
                        [
                            'attribute' => 'certificate.number',
                            'format' => 'raw',
                            'value' => function ($data) {
                                return Html::a($data->certificate->number, Url::to(['/certificates/view', 'id' => $data->certificate->id]));
                            }
                        ],
                        [
                            'label' => 'Процент',
                            'value' => function ($model) {


                                $completeness = (new \yii\db\Query())
                                    ->select(['completeness'])
                                    ->from('completeness')
                                    ->where(['contract_id' => $model->id])
                                    ->andWhere(['month' => 12])
                                    ->andWhere(['preinvoice' => 0])
                                    ->one();

                                return $completeness['completeness'];
                            }
                        ],
                        [
                            'label' => 'К оплате',
                            'value' => function ($data, $key, $index, $colum) {

                                $completeness = (new \yii\db\Query())
                                    ->select(['sum'])
                                    ->from('completeness')
                                    ->where(['contract_id' => $data->id])
                                    ->andWhere(['preinvoice' => 0])
                                    ->andWhere(['month' => 12])
                                    ->one();

                                /*
                                 $nopreinvoice = (new \yii\db\Query())
                                     ->select(['id'])
                                     ->from('invoices')
                                     ->where(['month' => 12])
                                     ->andWhere(['prepayment' => 1])
                                     ->andWhere(['status' => [0,1,2]])
                                     ->one();

                                 $precompleteness = (new \yii\db\Query())
                                         ->select(['sum'])
                                         ->from('completeness')
                                         ->where(['contract_id' => $data->id])
                                         ->andWhere(['preinvoice' => 1])
                                         ->andWhere(['month' => 12])
                                         ->one();

                                 if (!isset($nopreinvoice['id']) or empty($nopreinvoice['id'])) {
                                     return round($completeness['sum'] + $precompleteness['sum'], 2);
                                 }
                                 else { */

                                return round($completeness['sum'], 2);
                                //}
                            }
                        ],
                    ],
                ]);
            } else {
                echo GridView::widget([
                    'dataProvider' => $ContractsProvider,
                    'summary' => false,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'number',
                            'format' => 'raw',
                            'value' => function ($data) {
                                return Html::a($data->number, Url::to(['/contracts/view', 'id' => $data->id]));
                            }
                        ],
                        'date:date',
                        [
                            'attribute' => 'certificate.number',
                            'format' => 'raw',
                            'value' => function ($data) {
                                return Html::a($data->certificate->number, Url::to(['/certificates/view', 'id' => $data->certificate->id]));
                            }
                        ],
                        [
                            'label' => 'Процент',
                            'value' => function ($data, $key, $index, $colum) use ($model) {


                                $completeness = (new \yii\db\Query())
                                    ->select(['completeness'])
                                    ->from('completeness')
                                    ->where(['contract_id' => $data->id])
                                    ->andWhere(['month' => $model->month])
                                    ->andWhere(['preinvoice' => 0])
                                    ->one();

                                return $completeness['completeness'];
                            }
                        ],
                        [
                            'label' => 'К оплате',
                            'value' => function ($data, $key, $index, $colum) use ($model) {

                                $completeness = (new \yii\db\Query())
                                    ->select(['sum'])
                                    ->from('completeness')
                                    ->where(['contract_id' => $data->id])
                                    ->andWhere(['preinvoice' => 0])
                                    ->andWhere(['month' => $model->month])
                                    ->andWhere(['preinvoice' => 0])
                                    ->one();

                                /*
                                 $nopreinvoice = (new \yii\db\Query())
                                     ->select(['id'])
                                     ->from('invoices')
                                     ->where(['month' => date('m')])
                                     ->andWhere(['prepayment' => 1])
                                     ->andWhere(['status' => [0,1,2]])
                                     ->one();

                                 $precompleteness = (new \yii\db\Query())
                                     ->select(['sum'])
                                     ->from('completeness')
                                     ->where(['contract_id' => $data->id])
                                     ->andWhere(['preinvoice' => 1])
                                     ->andWhere(['month' => date('m')])
                                     ->one();

                                 if (!isset($nopreinvoice['id']) or empty($nopreinvoice['id'])) {
                                     return round($completeness['sum'] + $precompleteness['sum'], 2);
                                 }
                                 else { */

                                return round($completeness['sum'], 2);
                                // }

                            }
                        ],
                    ],
                ]);
            }
        } else {
            echo GridView::widget([
                'dataProvider' => $ContractsProvider,
                'summary' => false,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'number',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return Html::a($data->number, Url::to(['/contracts/view', 'id' => $data->id]));
                        }
                    ],
                    'date:date',
                    [
                        'attribute' => 'certificate.number',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return Html::a($data->certificate->number, Url::to(['/certificates/view', 'id' => $data->certificate->id]));
                        }
                    ],
                    [
                        'label' => 'Процент',
                        //'attribute' => $model->getPreinvoicecompleteness(),

                        'value' => function ($data, $key, $index, $colum) use ($model) {

                            $completeness = (new \yii\db\Query())
                                ->select(['completeness'])
                                ->from('completeness')
                                ->where(['contract_id' => $data->id])
                                ->andWhere(['month' => $model->month])
                                ->andWhere(['preinvoice' => 1])
                                ->one();

                            return $completeness['completeness'];
                        }
                    ],
                    [
                        'label' => 'К оплате',
                        'value' => function ($data, $key, $index, $colum) use ($model) {

                            $completeness = (new \yii\db\Query())
                                ->select(['sum'])
                                ->from('completeness')
                                ->where(['contract_id' => $data->id])
                                ->andWhere(['preinvoice' => 1])
                                ->andWhere(['month' => $model->month])
                                ->andWhere(['preinvoice' => 1])
                                ->one();

                            return round($completeness['sum'], 2);
                        }
                    ],
                ],
            ]);
        }
    }

    // }
    ?>

    <p>
        <?php
        if (isset($roles['organizations'])) {
            echo Html::a('Назад', ['/personal/organization-invoices'], ['class' => 'btn btn-primary']);
            if ($model->status == 0) {
                echo '&nbsp;';
                echo Html::a('Удалить', ['terminate', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы уверены что хотите удалить этот ' . $inv . '?',
                        'method' => 'post',
                    ],
                ]);
            }
        }
        if (isset($roles['payer'])) {
            echo Html::a('Назад', ['/personal/payer-invoices'], ['class' => 'btn btn-primary']);
            echo '&nbsp;';
            if ($model->status === \app\models\Invoices::STATUS_NOT_VIEWED) {
                echo Html::a('В работу', ['work', 'id' => $model->id], ['class' => 'btn btn-success']);
            }
            if ($model->status === \app\models\Invoices::STATUS_IN_THE_WORK) {
                echo Html::a('Вывести из обработки', ['roll-back', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data'  => ['toggle'    => 'tooltip',
                                'placement' => 'top',
                                'title'     => 'Статус счета изменится на "Не просмотрен"',
                    ]
                ]);
                echo Html::a('Оплачено', ['complete', 'id' => $model->id], ['class' => 'btn btn-success']);
            }
        }
        ?>
    </p>

</div>
