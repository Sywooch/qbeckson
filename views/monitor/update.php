<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserIdentity */

$this->title = 'Редактирование наблюдателя';
$this->params['breadcrumbs'][] = ['label' => 'Наблюдатели', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Обновление';
?>
<div class="user-identity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
