<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvoicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoices-index  col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?= var_dump($selection); ?>
</div>
