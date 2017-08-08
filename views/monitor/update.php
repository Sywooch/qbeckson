<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserIdentity */

$this->title = 'Редактирование организации';
$this->params['breadcrumbs'][] = ['label' => 'Уполномоченные организации', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="user-identity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
