<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProgrammeModule */

$this->title = 'Update Programme Module: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Модуль программы ' . $model->program->name, 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $model->name . ' (' . $model->year . ')', 'url' => ['view', 'id' => $model->id]
];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="programme-module-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
