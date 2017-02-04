<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Payers */

$this->title = 'Создать плательщика';
$this->params['breadcrumbs'][] = ['label' => 'Плательщики', 'url' => ['/personal/operator-payers']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payers-create  col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'user' => $user,
    ]) ?>

</div>
