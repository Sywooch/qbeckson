<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ProgrammeModule */

$this->title = $model->name;
$this->params['breadcrumbs'][] = [
    'label' => $model->program->name, 'url' => ['programs/view', 'id' => $model->program_id]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programme-module-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => ['id',
            'name',
            [
                'attribute' => 'program',
                'label' => 'Программа',
                'value' => Html::a($model->program->name, ['programs/view', 'id' => $model->program_id]),
                'format' => 'raw'
            ],
            'year',
            'month',
            'hours',
            'kvfirst',
            'kvdop',
            'hoursindivid',
            'hoursdop',
            'minchild',
            'maxchild',
            'price:currency',
            'normative_price:currency',
            'rating',
            'limits',
            'open:boolean',

            'quality_control',
            'p21z',
            'p22z',
            'results:ntext',
        ],
    ]) ?>

</div>
