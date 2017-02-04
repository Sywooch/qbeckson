<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CertGroup */

$this->title = 'Редактировать величину номинала сертификата для группы';
$this->params['breadcrumbs'][] = ['label' => 'Стоимость групп', 'url' => ['/cert-group/index']];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="cert-group-update   col-md-10  col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
