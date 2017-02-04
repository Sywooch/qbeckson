<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Cooperate */

$this->title = $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['/personal/payer-organizations']];
$this->params['breadcrumbs'][] = 'Соглашение: '.$this->title;
?>
<div class="cooperate-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'number',
            'organization_id',
            'payer_id',
            'date:date',
            //'date_dissolution',
            'status',
            //'reade',
        ],
    ]) ?>

    <?php
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
    if ($roles['payer']) {
        echo Html::a('Назад', '/personal/payer-organizations', ['class' => 'btn btn-primary']);
    }

    ?>

</div>
