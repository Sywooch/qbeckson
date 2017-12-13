<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Mun */

$isOperator = Yii::$app->user->can('operators');
$this->title = Yii::t('app', 'Редактировать муниципалитет: ', [
    'modelClass' => 'Mun',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Муниципалитеты'), 'url' => $isOperator ? ['index'] : null];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => $isOperator ? ['view', 'id' => $model->id] : null];
$this->params['breadcrumbs'][] = Yii::t('app', 'Редактировать');
?>
<div class="mun-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
