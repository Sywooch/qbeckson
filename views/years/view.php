<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ProgrammeModule */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ProgrammeModule'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="years-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'program_id',
            'verification',
            'year',
            'month',
            'hours',
            'kvfirst',
            'kvdop',
            'hoursindivid',
            'hoursdop',
            'minchild',
            'maxchild',
            'price',
            'normative_price',
            'rating',
            'limits',
            'open',
            'quality_control',
        ],
    ]) ?>

</div>
