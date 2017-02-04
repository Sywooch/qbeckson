<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CertGroup */

$this->title = 'Редактировать группу: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Стоимость групп', 'url' => ['/cert-group/index']];
$this->params['breadcrumbs'][] = ['label' => $model->group, 'url' => ['view', 'id' => $model->group]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="cert-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
