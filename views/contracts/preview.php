<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Organization;
use app\models\Certificates;
use app\models\Payers;
use kartik\datecontrol\DateControl;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */

$this->title = 'Предпросмотр договора';
$this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/organization-contracts']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-create">
   <?= Html::a('Назад', Url::to(['/contracts/generate', 'id' => $model->id]), ['class' => 'btn btn-primary']); ?>
    <?= Html::a('Просмотр договора', Url::to(['/contracts/mpdf', 'id' => $model->id]), ['class' => 'btn btn-primary']); ?>
    <?= Html::a('Подтвердить заявку', Url::to(['/contracts/ok', 'id' => $model->id]), ['class' => 'btn btn-primary']); ?> 

</div>
