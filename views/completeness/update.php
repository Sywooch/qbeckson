<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Completeness */

$date=explode(".", date("d.m.Y"));
            switch ($date[1] - 1){
            case 1: $m='январе'; break;
            case 2: $m='феврале'; break;
            case 3: $m='марте'; break;
            case 4: $m='апреле'; break;
            case 5: $m='мае'; break;
            case 6: $m='июне'; break;
            case 7: $m='июле'; break;
            case 8: $m='августе'; break;
            case 9: $m='сентябре'; break;
            case 10: $m='октябре'; break;
            case 11: $m='ноябре'; break;
            case 12: $m='декабре'; break;
            }

$this->title = 'Полнота услуг оказанных организацией';
  $this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
  $this->params['breadcrumbs'][] = ['label' => 'Полнота оказанных услуг в '.$m , 'url' => ['/groups/invoice']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="completeness-update col-md-10 col-md-offset-1">
    

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
