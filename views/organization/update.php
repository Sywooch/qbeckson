<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Organization */

$this->title = 'Редактировать организацию: ' . $model->name;

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if (isset($roles['operators'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['/personal/operator-organizations']];
}
if (isset($roles['organizations'])) {
    $this->params['breadcrumbs'][] = 'Организации';
}
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="organization-update  col-md-10  col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'user' => $user,
    ]) ?>

</div>
