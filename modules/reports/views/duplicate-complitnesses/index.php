<?php

use app\models\UserIdentity;
use app\widgets\SearchFilter;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\reports\models\DuplicateComplitnesses */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Договора с дубликатами комплитнесов';
$this->params['breadcrumbs'][] = ['label' => 'Отчеты', 'url' => ['/reports']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="contracts-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php Pjax::begin(); ?>
    <?= SearchFilter::widget([
        'model' => $searchModel,
        'action' => ['reports/duplicate-complitnesses'],
        'data' => $searchModel->getColumns(),
        'role' => UserIdentity::ROLE_ADMINISTRATOR,
        'type' => 'r-dupl-compl'
    ]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'pjax' => true,
        'summary' => false,
        'columns' => $searchModel->getColumns(true),
    ]); ?>
    <?php Pjax::end(); ?></div>
