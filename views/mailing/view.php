<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \app\models\mailing\repository\MailingListWithTasks */

$this->title = $model->subject;
$this->params['breadcrumbs'][] = ['label' => 'Рассылки', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Список рассылки';
?>
<div class="operators-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'state',
            'created_at:datetime',
            'countAllTasks',
            'subject',
            'message:html',
            'munsString',
            'targetsString',
            'lastActionTime:datetime'
        ],
    ]) ?>

</div>
