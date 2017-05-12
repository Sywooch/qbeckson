<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Organization */

$this->title = 'Заявка на добавление поставщика образовательных услуг';
?>
<div class="organization-create  col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="well">
        <b>Статус заявки:</b> <?= $model->statusName ?>
        <?php if ($model->isRefused): ?>
            <br /><br /><div class="alert alert-danger"><b>Причина отказа:</b> <?= nl2br($model->refuse_reason) ?></div>
        <?php endif; ?>
    </div>

    <?php
    if ($model->requestCanBeUpdated) {
        echo $this->render('_form', [
            'model' => $model,
        ]);
    }
    ?>

</div>
