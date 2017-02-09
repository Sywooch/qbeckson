<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Certificates */

$this->title = 'Добавить сертификат';

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if (isset($roles['operators'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['/personal/operator-certificates']];
}
if (isset($roles['payer'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['/personal/payer-certificates']];
}

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificates-create col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'user' => $user,
        'region' => $region,
        'payer' => $payer,
    ]) ?>

</div>
