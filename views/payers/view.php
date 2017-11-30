<?php

use app\models\Cooperate;
use app\models\forms\CooperateChangeTypeForm;
use app\models\Organization;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Payers */
/* @var $activeCooperateExists boolean */

$this->title = $model->name;

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if (isset($roles['operators'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Плательщики', 'url' => ['/personal/operator-payers']];
}
if (isset($roles['organizations'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Плательщики', 'url' => ['/personal/organization-payers']];
    if ($cooperation = $model->getCooperation()) {
        $commitments = \app\models\Contracts::getCommitments($cooperation->id);
        $summary = \app\models\Invoices::getSummary($cooperation->id);
    }
}
if (isset($roles['payer'])) {
    $this->params['breadcrumbs'][] = 'Плательщики';
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payers-view  col-md-offset-2 col-md-8">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'address_actual',
            [
                'attribute' => 'mun',
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var \app\models\Payers $model */
                    return $model->municipality->name;
                },
            ],
            'phone',
            'email:email',
            'fio',
            'position',
        ],
    ]) ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'address_legal',
            'OGRN',
            'INN',
            'KPP',
            'OKPO',
        ],
    ]) ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'directionality_1rob_count',
                'value' => $model->directionality1rob($model->id),
            ],
            [
                'attribute' => 'directionality_1_count',
                'value' => $model->directionality1($model->id),
            ],
            [
                'attribute' => 'directionality_2_count',
                'value' => $model->directionality2($model->id),
            ],
            [
                'attribute' => 'directionality_3_count',
                'value' => $model->directionality3($model->id),
            ],
            [
                'attribute' => 'directionality_4_count',
                'value' => $model->directionality4($model->id),
            ],
            [
                'attribute' => 'directionality_5_count',
                'value' => $model->directionality5($model->id),
            ],
            [
                'attribute' => 'directionality_6_count',
                'value' => $model->directionality6($model->id),
            ],
        ],
    ]) ?>
    <div>
        <?php
        if (isset($roles['operators'])) {
            $previus = (new Query())
                ->select(['id'])
                ->from('certificates')
                ->where(['payer_id' => $model->id])
                ->count();

            $cooperate = (new Query())
                ->select(['id'])
                ->from('cooperate')
                ->where(['payer_id' => $model->id])
                ->andWhere(['status' => 1])
                ->count();

            $display['previus'] = $previus;
            $display['cooperate'] = $cooperate;

            echo DetailView::widget([
                'model' => $display,
                'attributes' => [
                    [
                        'label' => Html::a(
                            'Число выданных сертификатов',
                            Url::to([
                                'personal/operator-certificates',
                                'CertificatesSearch[payer]' => $model->name,
                                'CertificatesSearch[payer_id]' => $model->id
                            ]),
                            ['class' => 'blue', 'target' => '_blank']
                        ),
                        'value' => $display['previus'],
                    ],
                    [
                        'label' => Html::a(
                            'Число заключенных соглашений',
                            Url::to([
                                'cooperate/index',
                                'CooperateSearch[payerName]' => $model->name,
                                'CooperateSearch[payer_id]' => $model->id
                            ]),
                            ['class' => 'blue', 'target' => '_blank']
                        ),
                        'value' => $display['cooperate'],
                    ],
                ],
            ]);

            echo Html::a('Назад', '/personal/operator-payers', ['class' => 'btn btn-primary']);
            echo '&nbsp;';
            echo Html::a('Редактировать', Url::to(['/payers/update', 'id' => $model->id]), ['class' => 'btn btn-primary']);

            if (!$previus) {
                echo '&nbsp;';
                echo Html::a('Удалить', Url::to(['/payers/delete', 'id' => $model->id]), ['class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы действительно хотите удалить этого плательщика?',
                        'method' => 'post']
                ]);
            }
        }

        if (isset($roles['payer'])) {
            echo Html::a('Назад', '/personal/payer-info', ['class' => 'btn btn-primary']);
        }
        if (isset($roles['organizations'])) {
            echo Html::a('Назад', '/personal/organization-payers', ['class' => 'btn btn-primary']);

            $organizations = new Organization();
            $organization = $organizations->getOrganization();

            $status = (new Query())
                ->select(['status'])
                ->from('cooperate')
                ->where(['payer_id' => $model->id])
                ->andWhere(['organization_id' => $organization['id']])
                ->andWhere(['status' => 1])
                ->column();
            if ($status) {
                $contracts = (new Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['payer_id' => $model->id])
                    ->andWhere(['organization_id' => $organization['id']])
                    ->count();
                if ($contracts === 0) {
                    echo ' ';
                    echo Html::a(
                        'Расторгнуть соглашение',
                        Url::to(['/cooperate/decooperate', 'id' => $model->id]),
                        [
                            'class' => 'btn btn-danger',
                            'data' => ['confirm' => 'Вы действительно хотите расторгнуть соглашение с этим плательщиком?'],
                            'title' => 'Расторгнуть соглашение'
                        ]
                    );
                }
            } else {
                $status2 = (new Query())
                    ->select(['status'])
                    ->from('cooperate')
                    ->where(['payer_id' => $model->id])
                    ->andWhere(['organization_id' => $organization['id']])
                    ->andWhere(['status' => 0])
                    ->column();

                if ($status2) {
                    echo ' ';
                    echo Html::a(
                        'Удалить соглашение',
                        Url::to(['cooperate/delete', 'id' => $model->id]),
                        [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Вы действительно хотите удалить соглашение с этим плательщиком?',
                                'method' => 'post'
                            ],
                            'title' => 'Расторгнуть соглашение'
                        ]
                    );
                }
            }

            if (null !== $cooperation) {
                if (count($cooperation->contracts) < 1) {
                    echo $this->render(
                        '../cooperate/reject-contract',
                        ['cooperation' => $cooperation]
                    );
                }

                $documentLabel = $cooperation->total_payment_limit ? 'Текст договора/соглашения c суммой' : 'Текст договора/соглашения без суммы';
                $alternativeDocumentLabel = !$cooperation->total_payment_limit ? 'Текст договора/соглашения c суммой' : 'Текст договора/соглашения без суммы';

                $activeDocumentLink = null !== $cooperation->getActiveDocumentUrl() ? Html::a($documentLabel, [$cooperation->getActiveDocumentUrl()], ['target' => 'blank']) : '';
                $alternativeDocumentLink = null !== $cooperation->getAlternativeDocumentUrl() ? Html::a($alternativeDocumentLabel, [$cooperation->getAlternativeDocumentUrl()], ['target' => 'blank']) : '';

                if ($activeDocumentLink) {
                    echo '<hr><div class="panel panel-default">
                        <div class="panel-body">
                        <p>Действующее соглашение:</p>
                        <br>' .
                        $activeDocumentLink .
                        ' </div>
                </div>';
                }

                if ($activeCooperateExists && $alternativeDocumentLink) {
                    echo '<hr><div class="panel panel-default">
                        <div class="panel-body">
                        <p>Альтернативное соглашение:</p>
                        <br>' .
                        $alternativeDocumentLink .
                        ' </div>
                </div>';
                }

                if (Yii::$app->user->can('organizations') && $cooperation->status === Cooperate::STATUS_APPEALED) {
                    echo '<p class="pull-right text-warning">Ваша жалоба ожидает рассмотрения оператором</p>';
                }

                if ($cooperation->status === Cooperate::STATUS_REJECTED) {
                    echo ' ';
                    echo $this->render(
                        '../cooperate/appeal-request',
                        ['model' => $cooperation]
                    );
                }

                if ($cooperation->status === Cooperate::STATUS_CONFIRMED &&
                    null === $cooperation->number &&
                    null === $cooperation->date
                ) {
                    echo $this->render(
                        '../cooperate/requisites',
                        [
                            'model' => $cooperation,
                            'label' => 'Сведения о реквизитах соглашения/договора не внесены',
                        ]
                    );
                    echo '<br /><br />';
                } elseif ($cooperation->status === Cooperate::STATUS_CONFIRMED &&
                    null !== $cooperation->number &&
                    null !== $cooperation->date
                ) {
                    echo '<p>Реквизиты соглашения: от ' . $cooperation->date . ' №' . $cooperation->number . '</p>';
                }
                if (!empty($commitments)) {
                    echo '<div style="margin-top: 20px;">Совокупная сумма подтвержденных обязательств по договору &ndash; ' . round($commitments, 2) . ' рублей</div>';
                }
                if (!empty($summary)) {
                    echo '<div style="margin-top: 20px;">Совокупная сумма оплаченных ранее счетов &ndash; ' . round($summary, 2) . ' рублей</div>';
                }
            } else {
                echo ' ';
                echo Html::a(
                    'Направить заявку на заключение соглашения с уполномоченной организацией',
                    Url::to(['cooperate/request', 'payerId' => $model->id]),
                    ['class' => 'btn btn-primary']
                );
            }
        }
        ?>
    </div>
</div>
