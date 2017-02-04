<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Operators */

$this->title = 'Редактировать оператора';
$this->params['breadcrumbs'][] = ['label' => 'Оператор', 'url' => ['/personal/operator-statistic']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operators-update col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
