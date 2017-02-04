<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Certificates */

$this->title = 'Редактировать сведения о сертификате: ' . $model->number;

$this->params['breadcrumbs'][] = ['label' => 'Сертификат', 'url' => ['/personal/certificate-info']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificates-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
