<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProgrammeModule */

$this->title = 'Установить цену: '. $model->program->name.' '.$model->year.' модуль';
$this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/organization-programs']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="years-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
