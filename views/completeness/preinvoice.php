<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Completeness */


$date=explode(".", date("d.m.Y"));
            switch ($date[1]){
           case 1: $m='январь'; break;
            case 2: $m='февраль'; break;
            case 3: $m='март'; break;
            case 4: $m='апрель'; break;
            case 5: $m='май'; break;
            case 6: $m='июнь'; break;
            case 7: $m='июль'; break;
            case 8: $m='август'; break;
            case 9: $m='сентябрь'; break;
            case 10: $m='октябрь'; break;
            case 11: $m='ноябрь'; break;
            case 12: $m='декабрь'; break;
            }

$this->title = 'Авансировать';
  $this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
  $this->params['breadcrumbs'][] = ['label' => 'Авансировать за '.$m , 'url' => ['/groups/preinvoice']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="completeness-update col-md-10 col-md-offset-1">
    

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'completeness')->textInput()->label(false) ?>
    
    <?php 
        if ($display) { echo '<h3>'.$display.'</h3>'; }
    ?>

    <div class="form-group">
       <?= Html::a('Назад', ['/groups/preinvoice'], ['class' => 'btn btn-primary']) ?>
&nbsp;
        <?= Html::submitButton($model->isNewRecord ? 'Сохранить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
