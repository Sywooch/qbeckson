<?php

use app\models\Cooperate;
use app\models\KeyStorageItem;
use trntv\filekit\widget\Upload;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\KeyStorageItem */
/* @var $form yii\bootstrap\ActiveForm */
//Мини кастыль для UI
$js = <<<JS
    $('#type').change(function() {
        var val = $(this).val();
        $('.item').hide();
        $('.' + val).show();
    })
JS;
$this->registerJs($js, $this::POS_READY);
?>
<div class="key-storage-item-form">
    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
    ]); ?>
        <?= $form->field($model, 'type')->dropDownList(array_merge(
            ['' => 'Выберите тип значения'],
            $model::types()
        )) ?>
        <?= $form->field($model, 'key')->dropDownList(array_merge(
            Cooperate::documentTypes()
        )) ?>
        <div class="item <?= KeyStorageItem::TYPE_STRING ?>" style="<?= (!$model->isNewRecord && $model->type === KeyStorageItem::TYPE_STRING) ? '' : 'display: none' ?>">
            <?= $form->field($model, 'value')->textInput() ?>
        </div>
        <div class="item <?= KeyStorageItem::TYPE_FILE ?>" style="<?= (!$model->isNewRecord && $model->type === KeyStorageItem::TYPE_FILE) ? '' : 'display: none' ?>">
            <?= $form->field($model, 'file')->widget(Upload::class, [
                'url' => ['file-storage/upload'],
                'maxFileSize' => 10 * 1024 * 1024,
                'acceptFileTypes' => new JsExpression('/(\.|\/)(doc|docx)$/i'),
            ]); ?>
        </div>
        <?= $form->field($model, 'comment')->textarea() ?>
        <div class="form-group">
            <?= Html::submitButton(
                $model->isNewRecord ? 'Создать' : 'Обновить',
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
            ) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
