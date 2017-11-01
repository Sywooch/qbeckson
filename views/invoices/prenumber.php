<?php

use app\helpers\DeclinationOfMonths;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Invoices */


$month = DeclinationOfMonths::getMonthNameByNumberAsNominative(
    (int)(new DateTime())->format('m')
);
$this->title = 'Введите номер для реестра авансов:';
  $this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
$this->params['breadcrumbs'][] = ['label' => 'Авансировать за ' . $month, 'url' => ['/groups/preinvoice']];
$this->params['breadcrumbs'][] = ['label' => 'Выберите плательщика', 'url' => ['/contracts/preinvoice']];
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
