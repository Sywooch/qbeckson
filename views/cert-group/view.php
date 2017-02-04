<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CertGroup */

$this->title = $model->group;
$this->params['breadcrumbs'][] = ['label' => 'Стоимость групп', 'url' => ['/cert-group/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cert-group-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            //'payer_id',
            'group',
            'nominal',
        ],
    ]) ?>
    
    <?= Html::a('Назад', '/cert-group/index', ['class' => 'btn btn-primary']) ?>

</div>
