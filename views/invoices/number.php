<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model app\models\Invoices */


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

$this->title = 'Введите номер для реестра счетов:';
  $this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
  $this->params['breadcrumbs'][] = ['label' => 'Полнота оказанных услуг в '.$m , 'url' => ['/groups/invoice']];
$this->params['breadcrumbs'][] = ['label' => 'Выберите плательщика', 'url' => ['/contracts/invoice']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoices-update  col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'date')->widget(DateControl::classname(), [
        'type'=>DateControl::FORMAT_DATE,
        'ajaxConversion'=>false,
        'options' => [
            'pluginOptions' => [
                'autoclose' => true
            ]
        ]
    ])
    ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <div class="form-group">
       <?= Html::a('Отмена', ['/personal/organization-invoices'], ['class' => 'btn btn-danger']) ?>
&nbsp;
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
