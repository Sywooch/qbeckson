<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Payers */

$this->title = 'Редактировать плательщика: ' . $model->name;

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if ($roles['operators']) {
    $this->params['breadcrumbs'][] = ['label' => 'Плательщики', 'url' => ['/personal/operator-payers']];
}
if (isset($roles['payer'])) {
    $this->params['breadcrumbs'][] = 'Плательщики';
}
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="payers-update col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="payers-form">

    <?= $this->render('_form', [
        'model' => $model,
        'user' => $user,
    ]) ?>
    </div>

</div>
