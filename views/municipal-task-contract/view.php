<?php

use app\components\widgets\modalCheckLink\ModalCheckLink;
use app\models\Certificates;
use app\models\Contracts;
use app\models\UserIdentity;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */


$this->title = 'Просмотр заявки';

$this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/organization-contracts']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-view col-md-8 col-md-offset-2">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    echo DetailView::widget([
        'model' => $model->organization,
        'attributes' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => Html::a($model->organization->name,
                    Url::to(['/organization/view-subordered', 'id' => $model->organization->id]), ['class' => 'blue', 'target' => '_blank']),
            ],

        ],
    ]);

    echo DetailView::widget([
        'model' => $model->certificate,
        'attributes' => [
            'fio_child',
            [
                'attribute' => 'fio_parent',
                'label' => 'Заказчик по договору',
            ],
            [
                'attribute' => 'number',
                'format' => 'raw',
                'value' => Html::a($model->certificate->number,
                    Url::to(['/certificates/view', 'id' => $model->certificate->id]),
                    ['class' => 'blue', 'target' => '_blank']),
            ],
        ],
    ]);
    ?>

    <?php
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'payers.name',
                'label' => 'Наименование плательщика',
                'format' => 'raw',
                'value' => Html::a($model->payer->name, Url::to(['/payers/view', 'id' => $model->payer->id]), ['class' => 'blue', 'target' => '_blank']),
            ]
        ],
    ]);
    ?>

    <?= DetailView::widget([
        'model' => $model->program,
        'attributes' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => Html::a($model->program->name, Url::to(['/programs/view', 'id' => $model->program->id]), ['class' => 'blue', 'target' => '_blank']),
            ],
            [
                'label' => 'Модуль',
                'value' => $model->group->year->fullname,
            ],
        ],
    ]) ?>

    <?php echo DetailView::widget([
        'model' => $model->group,
        'attributes' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => Html::a($model->group->name,
                    Url::to(['/groups/contracts', 'id' => $model->group->id]),
                    ['class' => 'blue', 'target' => '_blank']),
            ],
        ],
    ]);
    ?>

    <h3>Чтобы распечатать заявление &ndash; <a href="<?= Url::to('@pfdo/uploads/contracts/' . $model->pdf) ?>">жмите сюда</a></h3>

