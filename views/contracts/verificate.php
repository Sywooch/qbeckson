<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = 'Заявка на обучение';

 $this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/organization-contracts']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-view col-md-8 col-md-offset-2">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $cert,
        'attributes' => [
            [
                    'attribute' => 'number',
                    'format' => 'raw',
                    'value' => Html::a($cert->number, Url::to(['/certificates/view', 'id' => $cert->id]), ['class' => 'blue', 'target' => '_blank']),
            ],
            'fio_child',
            //'nominal',
            //'balance',
           
        ],
    ]) ?>
    
    <?= DetailView::widget([
        'model' => $group,
        'attributes' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => Html::a($group->name, Url::to(['/groups/contracts', 'id' => $group->id]), ['class' => 'blue', 'target' => '_blank']),
            ],
            //'address',
            //'schedule',
            //'datestart',
            //'datestop',
        ],
    ]) ?>
    
    <?= DetailView::widget([
        'model' => $program,
        'attributes' => [
            [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => Html::a($program->name, Url::to(['/programs/view', 'id' => $program->id]), ['class' => 'blue', 'target' => '_blank']),
            ],
        ],
    ]) ?>
    
    <?php  if ($model->status == 3) {
            echo DetailView::widget([
                'model' => $program,
                'attributes' => [
                    [
                    'label' => 'Посмотреть текст договора',
                    'format'=>'raw',
                    'value'=> Html::a('<span class="glyphicon glyphicon-download-alt"></span>', Url::to(['/contracts/mpdf', 'id' => $model->id])),
                    ],
                ],
            ]);            
        }
    ?>

    <?= Html::a('Назад', Url::to(['/personal/organization-contracts', 'id' => $model->id]), ['class' => 'btn btn-primary']); ?>
    <?php
    if ($model->status == 0) {
        echo Html::a('Продолжить', Url::to(['/contracts/generate', 'id' => $model->id]), ['class' => 'btn btn-primary']);
    }
    if ($model->status == 3) {
        echo Html::a('Продолжить', Url::to(['/contracts/save', 'id' => $model->id]), ['class' => 'btn btn-primary']);
    }
    ?>
    <div class="pull-right">
        <?= Html::a('Отказать', Url::to(['/contracts/no', 'id' => $model->id]), ['class' => 'btn btn-danger']) ?>
    </div>
</div>
