<?php

use app\models\Cooperate;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Cooperate */

$this->title = $model->number ? 'Просмотр соглашения ' . $model->number : 'Обработка соглашения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cooperate-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'number',
                'label'     => 'Реквизиты',
                'format'    => 'raw',
                'visible'   => (in_array($model->status, [Cooperate::STATUS_REJECTED, Cooperate::STATUS_APPEALED])) ? false : true,
                'value'     => function ($model)
                {
                    /** @var \app\models\Cooperate $model */
                    return $model->number . ' от ' . Yii::$app->formatter->asDate($model->date);
                },
            ],
            [
                'attribute' => 'organizationName',
                'label'     => 'Организация',
                'format'    => 'raw',
                'value'     => function ($model)
                {
                    /** @var \app\models\Cooperate $model */
                    return Html::a(
                        $model->organization->name,
                        Url::to(['organization/view', 'id' => $model->organization->id]),
                        ['target' => '_blank', 'data-pjax' => '0']
                    );
                },
            ],
            [
                'attribute' => 'payerName',
                'label' => 'Плательщик',
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var \app\models\Cooperate $model */
                    return Html::a(
                        $model->payer->name,
                        Url::to(['payers/view', 'id' => $model->payer->id]),
                        ['target' => '_blank', 'data-pjax' => '0']
                    );
                },
            ],
            [
                'attribute' => 'reject_reason',
                'format'    => 'ntext',
                'visible'   => (!in_array($model->status, [Cooperate::STATUS_REJECTED, Cooperate::STATUS_APPEALED])) ? false : true,
            ],
            [
                'attribute' => 'appeal_reason',
                'format'    => 'ntext',
                'visible'   => (!in_array($model->status, [Cooperate::STATUS_REJECTED, Cooperate::STATUS_APPEALED])) ? false : true,
            ],
            'created_date',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    /** @var \app\models\Cooperate $model */
                    return $model::statuses()[$model->status];
                }
            ]
        ],
    ]) ?>
    <?php
    if (Yii::$app->user->can('payer')) {
        echo Html::a('Назад', 'personal/payer-organizations', ['class' => 'btn btn-primary']);
    }
    if (Yii::$app->user->can('operators') && $model->status === \app\models\Cooperate::STATUS_APPEALED) {
        echo Html::a(
            'Отклонить жалобу',
            ['cooperate/reject-appeal', 'id' => $model->id],
            ['class' => 'btn btn-danger']
        );
        echo ' ';
        echo Html::a(
            'Вернуть на рассмотрение',
            ['cooperate/confirm-appeal', 'id' => $model->id],
            ['class' => 'btn btn-primary']
        );
    }
    ?>
</div>
