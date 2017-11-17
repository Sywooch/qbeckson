<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MunicipalTaskContract */

$this->title = 'Create Municipal Task Contract';
$this->params['breadcrumbs'][] = ['label' => 'Municipal Task Contracts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="municipal-task-contract-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
