<?php

/**
 * страница загрузки шаблона импорта списка сертификатов
 *
 * @var $certificateImportTemplate \app\models\certificates\CertificateImportTemplate
 */

use app\models\certificates\CertificateImportTemplate;
use trntv\filekit\widget\Upload;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Загрузка шаблона импорта списка сертификатов';

?>
<div class="import-form">
    <h1><?= $this->title ?></h1>
    <?php $form = ActiveForm::begin() ?>

    <?= $form->field($certificateImportTemplate, 'certificateImportTemplate')->widget(Upload::class, [
        'url' => ['file-storage/upload'],
        'maxFileSize' => 10 * 1024 * 1024,
        'acceptFileTypes' => new JsExpression('/(\.|\/)(xlsx)$/i'),
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('загрузить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>
