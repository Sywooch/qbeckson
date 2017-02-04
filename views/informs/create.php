<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Informs */

$this->title = 'Create Informs';
$this->params['breadcrumbs'][] = ['label' => 'Informs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="informs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
