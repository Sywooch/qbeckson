<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \app\models\mailing\decorators\MailingListDecorator */

$this->title = $model->subject;
$this->params['breadcrumbs'][] = 'Список рассылки';
?>
<div class="operators-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'state',
            'subject',
            'created_at:datetime'
        ],
    ]) ?>

</div>
