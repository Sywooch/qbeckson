<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CoefficientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Coefficients');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coefficient-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Coefficient'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'p21v',
            'p21s',
            'p21o',
            'p22v',
            // 'p22s',
            // 'p22o',
            // 'p3v',
            // 'p3s',
            // 'p3n',
            // 'weekyear',
            // 'weekmonth',
            // 'pk',
            // 'norm',
            // 'potenc',
            // 'ngr',
            // 'sgr',
            // 'vgr',
            // 'chr1',
            // 'zmr1',
            // 'chr2',
            // 'zmr2',
            // 'blimrob',
            // 'blimtex',
            // 'blimest',
            // 'blimfiz',
            // 'blimxud',
            // 'blimtur',
            // 'blimsoc',
            // 'ngrp',
            // 'sgrp',
            // 'vgrp',
            // 'ppchr1',
            // 'ppzm1',
            // 'ppchr2',
            // 'ppzm2',
            // 'ocsootv',
            // 'ocku',
            // 'ocmt',
            // 'obsh',
            // 'ktob',
            // 'vgs',
            // 'sgs',
            // 'pchsrd',
            // 'pzmsrd',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
