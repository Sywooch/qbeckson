<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Help */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Руководство', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="help-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
