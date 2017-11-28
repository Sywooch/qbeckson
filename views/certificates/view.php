<?php

use app\helpers\PermissionHelper;
use app\models\Contracts;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Certificates */
/* @var $freezer \app\models\certificates\FreezeUnFreezeCertificate */
/* @var $nerfer \app\models\certificates\CertificateNerfNominal */

$this->title = $model->number;

if (Yii::$app->user->can('operators')) {
    $this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['/personal/operator-certificates']];
}
if (Yii::$app->user->can('payer')) {
    $this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['/personal/payer-certificates']];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificates-view col-md-8 col-md-offset-2">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'fio_child',
            'fio_parent',
            [
                'label' => 'Плательщик',
                'format' => 'raw',
                'value'=> Html::a($model->payers->name, Url::to(['/payers/view', 'id' => $model->payers->id]), ['class' => 'blue', 'target' => '_blank']),
                'visible' => !Yii::$app->user->can('payer')
            ],
        ],
    ]); ?>

    <?php if (Yii::$app->user->can('operators') || Yii::$app->user->can('payer')) {
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'nominal',
                'certGroup.group',
                [
                    'attribute' => 'rezerv',
                    'value' => round($model->rezerv, 2),
                ],
                'balance',
                [
                    'label'=> Html::a(
                        'Число заключенных договоров',
                        Yii::$app->user->can('operators') ?
                            [
                                'personal/operator-contracts',
                                'SearchActiveContracts[certificateNumber]' => $model->number,
                                'SearchActiveContracts[certificate_id]' => $model->id,
                            ] :
                            [
                                'personal/payer-contracts',
                                'SearchActiveContracts[certificateNumber]' => $model->number,
                                'SearchActiveContracts[certificate_id]' => $model->id,
                            ],
                        ['class' => 'blue', 'target' => '_blank']
                    ),
                    'value' => Contracts::getCountContracts(['certificateId' => $model->id]),
                ],
            ],
        ]);
        echo \app\components\widgets\ContractPayDetails\ContractPayDetails::widget(
            [
                'query' => $completenessQuery
            ]
        );
    } ?>

    <p>
        <?php if (Yii::$app->user->can('operators')) {
            echo Html::a('Назад', '/personal/operator-certificates', ['class' => 'btn btn-primary']);
        } elseif (Yii::$app->user->can('organizations')) {
            echo Html::a('Назад', '/personal/organization-favorites', ['class' => 'btn btn-primary']);
        }

        if (Yii::$app->user->can('payer')) {
            echo '<div class="pull-right">';
            if (PermissionHelper::checkMonitorUrl('/certificates/actual') /*&& $model->canFreez()*/) {
                if ($model->actual) {
                    if ($freezer->canFreeze) {
                        echo \app\components\widgets\modalCheckLink\ModalCheckLink::widget([
                            'link' => Html::a('Заморозить', Url::to(['/certificates/noactual', 'id' => $model->id]),
                                ['class' => 'btn btn-danger']),
                            'buttonOptions' => ['label' => 'Заморозить', 'class' => 'btn btn-danger'],
                            'content' => 'В случае если Вы заморозите сертификат все средства его баланс будет обнулен. Повторно разморозить сертификат не удастся до наступления следующего периода программы персонифицированного финансирования. Вы уверены, что имеете достаточные основания для заморозки сертификата в соответствии с правилами персонифицированного финансирования?',
                            'label' => 'Да, я уверен, что хочу заморозить сертификат.',
                            'title' => 'Заморозить сертификат?'
                        ]);
                    } else {
                        echo \app\components\widgets\ButtonWithInfo::widget([
                            'label' => 'Заморозить',
                            'message' => $freezer->firstErrorAsString,
                            'options' => ['disabled' => 'disabled',
                                'class' => 'btn btn-theme',]
                        ]);
                    }
                    echo '&nbsp;';
                    if ($nerfer->canNerf) {

                        echo Html::a('Уменьшить номинал', Url::to(['/certificates/nerf-nominal', 'id' => $model->id]), ['class' => 'btn btn-primary']);
                    } else {
                        echo \app\components\widgets\ButtonWithInfo::widget([
                            'label' => 'Уменьшить номинал',
                            'message' => $nerfer->firstErrorAsString,
                            'options' => ['disabled' => 'disabled',
                                'class' => 'btn btn-primary',]
                        ]);
                    }



                } else {
                    if ($freezer->canUnFreeze) {
                        echo Html::a('Активировать', Url::to(['/certificates/actual', 'id' => $model->id]), ['class' => 'btn btn-success']);
                    } else {
                        echo \app\components\widgets\ButtonWithInfo::widget([
                            'label' => 'Активировать',
                            'message' => $freezer->firstErrorAsString,
                            'options' => ['disabled' => 'disabled',
                                'class' => 'btn btn-primary',]
                        ]);
                    }
                }
                echo '&nbsp;';
            }
            if (!$model->hasContracts && PermissionHelper::checkMonitorUrl('/certificates/delete')) {
                echo Html::a('Удалить', Url::to(['/certificates/delete', 'id' => $model->id]), ['class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Уверены, что хотите удалить сертификат?',
                        'method' => 'post'
                    ]]);
            }
            echo '</div>';

            echo Html::a('Назад', '/personal/payer-certificates', ['class' => 'btn btn-primary']);
            echo '&nbsp;';
            if (PermissionHelper::checkMonitorUrl('/certificates/update')) {
                echo Html::a('Редактировать', Url::to(['/certificates/update', 'id' => $model->id]), ['class' => 'btn btn-primary']);
                echo '&nbsp;';
            }
        }

        ?>
    </p>
</div>
