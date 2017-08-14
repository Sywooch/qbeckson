<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Cooperate */

$this->title = 'Введите реквизиты соглашения';
$this->params['breadcrumbs'][] = ['label' => 'Плательщики', 'url' => ['/personal/organization-payers']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cooperate-create col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    'id' => $id,
    ]) ?>

</div>
