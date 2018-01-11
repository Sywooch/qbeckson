<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $model \app\models\forms\ContractRemoveForm */

$statusInputName = Html::getInputName($model, 'status');
?>
<div class="modal fade text-left" id="form-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Внимание!</h4>
            </div>
            <div class="modal-body">
                <p>После удаления договор восстановить будет невозможно! <br>
                    Убедитесь, что есть достаточные основания для его удаления.</p>
                <p>Договор №<span id="modal-contract-number"></span> от <span id="modal-contract-date"></span> года.</p>
                <p>Заключён по сертификату ПФ <span id="modal-certificate-number"></span>.</p>
                <p>Основание удаления: <span id="modal-delete-reason"></span>.</p>
                <p><a href="#" id="modal-delete-document" target="_blank">Скачать подтверждающий документ</a>.</p>
                <p><strong id="modal-alert-message" class="text-danger"></strong></p>
            </div>
            <div class="modal-footer">
                <?php $form = ActiveForm::begin([
                    'action' => ['resolution'],
                    'options' => ['class' => 'form-inline'],
                ]); ?>
                <?= Html::activeHiddenInput($model, 'contractId', ['id' => 'modal-contract-id']) ?>
                <?= Html::activeHiddenInput($model, 'appId', ['id' => 'modal-app-id']) ?>
                <?= $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::className())
                    ->hint('Поле доступно после проверки подтверждающего документа') ?>
                <br>
                <?= Html::submitButton('Продолжить',
                    [
                        'id' => 'modal-confirm-button',
                        'class' => 'btn btn-success',
                        'name' => $statusInputName,
                        'value' => '1'
                    ]); ?>
                <?= Html::submitButton('Отклонить',
                    ['class' => 'btn btn-danger', 'name' => $statusInputName, 'value' => '0']); ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

