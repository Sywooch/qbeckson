<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Organization */

$this->title = 'Редактировать поставщика: ' . $model->name;

if (Yii::$app->user->can('operators')) {
    $this->params['breadcrumbs'][] = ['label' => 'Поставщики', 'url' => ['/personal/operator-organizations']];
} elseif (Yii::$app->user->can('organizations')) {
    $this->params['breadcrumbs'][] = 'Поставщики';
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
