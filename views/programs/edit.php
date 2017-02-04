<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = 'Редактировать программу: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/organization-programs']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="programs-update">

    <h1><?= Html::encode($this->title) ?></h1>

     <?= $this->render('_form', [
        'model' => $model,
        'file' => $file,
        'modelsYears' => $modelYears
    ]) ?>

</div>
