<?php

use app\models\Cooperate;
use app\models\OperatorSettings;
use app\models\Organization;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Payers */
/* @var $operatorSettings OperatorSettings */
/* @var $futurePeriodCooperate Cooperate */

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
        if (isset($roles['organizations'])) { ?>

            <?php if (isset($cooperation) && Cooperate::STATUS_NEW == $cooperation->status): ?>
                <p>Вы отправили заявку на заключение договора действующего <?= $cooperation->getPeriodValidityLabel() ?>.</p>
            <?php endif; ?>

            <?php
            if ($cooperation && Cooperate::STATUS_NEW == $cooperation->status) {
                echo ' ';
                echo Html::a(
                    'Отменить заявку',
                    Url::to(['cooperate/delete', 'id' => $model->id]),
                    [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Вы действительно хотите отменить заявку?',
                            'method' => 'post'
                        ],
                        'title' => 'Отменить заявку'
                    ]
                );
            }

            if (null !== $cooperation) {
                if (count($cooperation->contracts) < 1 && $cooperation->status != Cooperate::STATUS_NEW) {
                    echo $this->render(
                        '../cooperate/reject-contract',
                        ['cooperation' => $cooperation]
                    );
                }

                $documentLabel = $cooperation->total_payment_limit ? 'Текст договора/соглашения c суммой' : 'Текст договора/соглашения без суммы';

                $activeDocumentLink = null !== $cooperation->getActiveDocumentUrl() ? Html::a($documentLabel, [$cooperation->getActiveDocumentUrl()], ['target' => 'blank']) : '';

                if ($activeDocumentLink) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?= Cooperate::documentNames()[$operatorSettings->document_name] ?>, используемый для оплаты услуг на текущий момент:
                        </div>

                        <table class="table table-striped table-bordered detail-view">
                            <tbody>
                            <tr>
                                <th>Тип соглашения</th>
                                <td>
                                    <?= $activeDocumentLink ?>
                                </td>
                            </tr>

                            <?php if ($cooperation->status === Cooperate::STATUS_CONFIRMED &&
                                null === $cooperation->number &&
                                null === $cooperation->date
                            ) { ?>
                                <tr>
                                    <th>Реквизиты соглашения</th>
                                    <td>
                                        <?= $this->render(
                                            '../cooperate/requisites',
                                            [
                                                'model' => $cooperation,
                                                'toggleButtonClass' => null,
                                                'label' => 'Сведения о реквизитах соглашения/договора не внесены',
                                            ]
                                        ); ?>
                                    </td>
                                </tr>
                            <?php } else if (($cooperation->status === Cooperate::STATUS_CONFIRMED &&
                                    null !== $cooperation->number &&
                                    null !== $cooperation->date) ||
                                $cooperation->status === Cooperate::STATUS_ACTIVE
                            ) { ?>

                                <tr>
                                    <th>Реквизиты соглашения</th>
                                    <td>Договор от <?= \Yii::$app->formatter->asDate($cooperation->date) ?> №<?= $cooperation->number ?>
                                    </td>
                                </tr>
                                <?php if ($cooperation->status == Cooperate::STATUS_ACTIVE && $cooperation->document_type == Cooperate::DOCUMENT_TYPE_EXTEND) { ?>
                                    <tr>
                                        <th>Установлена максимальная сумма по договору, рублей</th>
                                        <td>
                                            <?= round($cooperation->total_payment_limit, 2) ?>
                                        </td>
                                    </tr>
                                <?php }
                            } ?>

                            <tr>
                                <th>Период действия соглашения</th>
                                <td>
                                    <?= $cooperation->getPeriodValidityLabel() ?>
                                </td>
                            </tr>

                            <?php if (!empty($commitments)): ?>
                                <tr>
                                    <th>Совокупная сумма подтвержденных обязательств по договору</th>
                                    <td>
                                        <?= round($commitments, 2) . ' рублей'; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if (!empty($summary)): ?>
                                <tr>
                                    <th>Совокупная сумма оплаченных ранее счетов</th>
                                    <td>
                                        <?= round($summary, 2) . ' рублей'; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php }

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

            } else {
                echo $this->render('_cooperate-request', ['model' => $model, 'operatorSettings' => $operatorSettings]);
            }

            if ($futurePeriodCooperate): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= Cooperate::documentNames()[$operatorSettings->document_name] ?>, использование которого предусматривается в будущем периоде:
                    </div>

                    <table class="table table-striped table-bordered detail-view">
                        <tbody>
                        <tr>
                            <th>Тип соглашения</th>
                            <td>
                                <?= Cooperate::documentTypes()[$futurePeriodCooperate->document_type] ?>
                            </td>
                        </tr>

                        <?php if (($futurePeriodCooperate->status === Cooperate::STATUS_CONFIRMED &&
                                null !== $futurePeriodCooperate->number &&
                                null !== $futurePeriodCooperate->date) ||
                            $futurePeriodCooperate->status === Cooperate::STATUS_ACTIVE
                        ) { ?>
                            <tr>
                                <th>Реквизиты соглашения</th>
                                <td>
                                    Договор от <?= \Yii::$app->formatter->asDate($futurePeriodCooperate->date) ?> №<?= $futurePeriodCooperate->number ?>
                                </td>
                            </tr>
                            <?php if ($futurePeriodCooperate->status == Cooperate::STATUS_ACTIVE && $futurePeriodCooperate->document_type == Cooperate::DOCUMENT_TYPE_EXTEND): ?>
                                <tr>
                                    <th>Установлена максимальная сумма по договору, рублей</th>
                                    <td>
                                        <?= round($futurePeriodCooperate->total_payment_limit, 2) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php } ?>

                        <?php if (!empty($commitments)): ?>
                            <tr>
                                <th>Совокупная сумма подтвержденных обязательств по договору</th>
                                <td>
                                    <?= round($commitments, 2) . ' рублей'; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($summary)): ?>
                            <tr>
                                <th>Совокупная сумма оплаченных ранее счетов</th>
                                <td>
                                    <?= round($summary, 2) . ' рублей'; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            <?php endif; ?>

            <?= Html::a('Назад', '/personal/organization-payers', ['class' => 'btn btn-primary']); ?>
        <?php } ?>
    </div>
</div>
