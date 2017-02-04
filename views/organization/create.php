<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Organization */

$this->title = 'Добавить организацию';
$this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['/personal/operator-organizations']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-create  col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'user' => $user,
    ]) ?>

</div>
