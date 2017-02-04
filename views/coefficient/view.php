<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Coefficient */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Coefficients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coefficient-view">

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
            'p21v',
            'p21s',
            'p21o',
            'p22v',
            'p22s',
            'p22o',
            'p3v',
            'p3s',
            'p3n',
            'weekyear',
            'weekmonth',
            'pk',
            'norm',
            'potenc',
            'ngr',
            'sgr',
            'vgr',
            'chr1',
            'zmr1',
            'chr2',
            'zmr2',
            'blimrob',
            'blimtex',
            'blimest',
            'blimfiz',
            'blimxud',
            'blimtur',
            'blimsoc',
            'ngrp',
            'sgrp',
            'vgrp',
            'ppchr1',
            'ppzm1',
            'ppchr2',
            'ppzm2',
            'ocsootv',
            'ocku',
            'ocmt',
            'obsh',
            'ktob',
            'vgs',
            'sgs',
            'pchsrd',
            'pzmsrd',
        ],
    ]) ?>

</div>
