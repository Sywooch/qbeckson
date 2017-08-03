<?php

/* @var $this yii\web\View */
/* @var $model app\models\CertificateInformation */

$this->title = 'Информация о получении сертификата';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificate-information-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
