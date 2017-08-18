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
$js = <<<JS
    $('#type').change(function() {
        var val = $(this).val();
        $('.item').hide().children('.text').hide();
        $('.' + val).show().children('.' + val + 'Text').show();
    })
    $('#iscustomvalue').change(function() {
        if(this.checked) {
            $('.extend').show();
            return;
        }
        $('.extend').hide();
    })
JS;
$this->registerJs($js, $this::POS_READY);
/** @var \app\models\Operators $operator */
$operator = Yii::$app->operator->identity;
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
        <div class="item <?= Cooperate::DOCUMENT_TYPE_GENERAL ?>">
            <p class="text <?= Cooperate::DOCUMENT_TYPE_GENERAL ?>Text">
                <small>* Рекомендуется заключать в случае если для расходования средств не требуется постановки расходного обязательства в казначействе (подходит для СОНКО)</small>
                <br>
                <?= Html::a('Просмотр договора', $operator->settings->getGeneralDocumentUrl()); ?>
            </p>
        </div>
        <div class="item <?= Cooperate::DOCUMENT_TYPE_CUSTOM ?>" style="display: none">
            <p class="text <?= Cooperate::DOCUMENT_TYPE_CUSTOM ?>Text" style="display: none;">
                <small>* Вы можете сделать свой вариант договора (например, проставить заранее реквизиты в предлагаемый оператором), но не уходите от принципов ПФ. Если Вы выберите данный вариант, укажите, пожалуйста, сделан ли Ваш договор с указанием максимальной суммы (в этом случае укажите сумму), или без нее, чтобы система отслеживала необходимость заключения допсоглашений и информировала Вас об этом при необходимости.</small>
            </p>
            <?= $form->field($model, 'isCustomValue')->checkbox(); ?>
            <?= $form->field($model, 'document')->widget(Upload::class, [
                'url' => ['file-storage/upload'],
                'maxFileSize' => 10 * 1024 * 1024,
                'acceptFileTypes' => new JsExpression('/(\.|\/)(doc|docx)$/i'),
            ]); ?>
        </div>
        <div class="item <?= Cooperate::DOCUMENT_TYPE_EXTEND ?>" style="display: none">
            <p class="text <?= Cooperate::DOCUMENT_TYPE_EXTEND ?>Text" style="display: none;">
                <small>* Рекомендуется заключать в случае если для постановки расходного обязательства на исполнение необходимо зафиксировать сумму договора (подходит для АУ). Использование данного договора предполагает необходимость регулярного заключения дополнительных соглашений (информационная система будет давать подсказки)</small>
                <br>
                <?= Html::a('Просмотр договора', $operator->settings->getExtendDocumentUrl()); ?>
            </p>
            <?= $form->field($model, 'value')->textInput() ?>
        </div>
        <div class="form-group clearfix">
            <?= Html::submitButton('Одобрить заявку', ['class' => 'btn btn-success pull-right']) ?>
        </div>
    <?php ActiveForm::end(); ?>
<?php Modal::end(); ?>
