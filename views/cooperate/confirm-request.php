<?php

use app\models\Cooperate;
use app\models\forms\ConfirmRequestForm;
use trntv\filekit\widget\Upload;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $cooperation app\models\Cooperate */
/* @var $model ConfirmRequestForm */
/* @var $form yii\widgets\ActiveForm */

$model = new ConfirmRequestForm();

//Мини кастыль для UI
$typeExtend = Cooperate::DOCUMENT_TYPE_EXTEND;
$typeCustom = Cooperate::DOCUMENT_TYPE_CUSTOM;
$js = <<<JS
    $('#type').change(function() {
        var val = $(this).val();
        if (val === '{$typeExtend}') {
            $('.item').hide();
            $('.extend').show();
        } else if (val === '{$typeCustom}') {
            $('.item').hide();
            $('.custom').show();
        } else {
            $('.item').hide();
        }
    })
JS;
$this->registerJs($js, $this::POS_READY);
?>
<?php
Modal::begin([
    'header' => '<h2>Одобрить заявку</h2>',
    'toggleButton' => [
        'tag' => 'a',
        'class' => 'btn btn-primary',
        'label' => 'Одобрить',
    ],
]);
?>
    <?php $form = ActiveForm::begin([
        'id' => 'cooperate-confirm-request-form',
        'action' => ['cooperate/confirm-request', 'id' => $cooperation->id],
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
    ]); ?>
        <?= $form->field($model, 'type')->dropDownList(Cooperate::documentTypes()) ?>
        <div class="item extend" style="display: none">
            <?= $form->field($model, 'value')->textInput() ?>
        </div>
        <div class="item custom" style="display: none">
            <?= $form->field($model, 'document')->widget(Upload::class, [
                'url' => ['file-storage/upload'],
                'maxFileSize' => 10 * 1024 * 1024,
                'acceptFileTypes' => new JsExpression('/(\.|\/)(doc|docx)$/i'),
            ]); ?>
        </div>
        <div class="form-group clearfix">
            <?= Html::submitButton('Одобрить заявку', ['class' => 'btn btn-success pull-right']) ?>
        </div>
    <?php ActiveForm::end(); ?>
<?php Modal::end(); ?>
