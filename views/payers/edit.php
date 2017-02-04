<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Payers */

$this->title = 'Редактировать сведения о плательщике: ' . $model->name;
$this->params['breadcrumbs'][] = 'Плательщики';
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="payers-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="payers-form">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    </div>

</div>
