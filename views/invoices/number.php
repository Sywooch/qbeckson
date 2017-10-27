<?php

use app\components\halpers\DeclinationOfMonths;
use app\components\widgets\modalCheckLink\ModalCheckLink;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\models\invoices\InvoiceBuilder */


$month = DeclinationOfMonths::getMonthNameByNumberAsPrepositional(
    (int)(new DateTime())->modify('previous month')->format('m')
);
$this->title = 'Введите номер для реестра счетов:';
  $this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
$this->params['breadcrumbs'][] = ['label' => 'Полнота оказанных услуг в ' . $month, 'url' => ['/groups/invoice']];
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
        <?php
        if ($model->haveOutOfRangeContracts) {
            echo ModalCheckLink::widget([
                'link' => Html::submitButton('Сохранить', ['class' => 'btn btn-success']),
                'buttonOptions' => ['label' => 'Сохранить', 'class' => 'btn btn-success'],
                'content' => Yii::t('yii', 'Внимание! Есть заявки на обучение и неакцептированные договоры со сроком начала обучения в
{month}
. Если Вы продолжите, то они будут автоматически отклонены, поскольку в ином случае проплата по ним не пройдет. Вы уверены, что хотите создать счет и потерять указанные заявки?', ['month' => $month]),
                'title' => 'Сохранить счет',
                'label' => 'Да, я уверен, что хочу сохранить счет.',
            ]);
        } else {
            echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']);
        }


        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
