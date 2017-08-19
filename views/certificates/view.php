<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\models\Contracts;
use app\helpers\PermissionHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Certificates */

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
                'rezerv',
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
    } ?>

    <p>
        <?php if (Yii::$app->user->can('operators')) {
            echo Html::a('Назад', '/personal/operator-certificates', ['class' => 'btn btn-primary']);
        } elseif (Yii::$app->user->can('organizations')) {
            echo Html::a('Назад', '/personal/organization-favorites', ['class' => 'btn btn-primary']);
        }

        if (Yii::$app->user->can('payer')) {
            echo '<div class="pull-right">';
            if (PermissionHelper::checkMonitorUrl('/certificates/delete')) {
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
