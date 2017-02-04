<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Completeness */

$this->title = 'Create Completeness';
$this->params['breadcrumbs'][] = ['label' => 'Completenesses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="completeness-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
