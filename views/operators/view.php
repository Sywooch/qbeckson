<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Operators */

$this->title = $model->name;
$this->params['breadcrumbs'][] = 'Оператор';
?>
<div class="operators-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'OGRN',
            'INN',
            'KPP',
            'OKPO',
            'address_legal',
            'address_actual',
            'phone',
            'email:email',
            'position',
            'fio',
        ],
    ]) ?>

</div>
