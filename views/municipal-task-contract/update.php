<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MunicipalTaskContract */

$this->title = 'Update Municipal Task Contract: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Municipal Task Contracts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="municipal-task-contract-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
