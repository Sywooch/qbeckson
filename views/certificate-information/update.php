<?php

use trntv\filekit\widget\Upload;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CertificateInformation */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Информация о получении сертификата';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificate-information-create">
    <div class="certificate-information-form">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <?= $form->errorSummary($model) ?>
        <?= $form->field($model, 'children_category')->textInput(['maxlength' => true]) ?>
        <div class="well">
            <p class="lead">Заявления на предоставление сертификата принимаются:</p>
            <?= $form->field($model, 'organization_name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'work_time')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>
        </div>
        <?= $form->field($model, 'rules')->textarea(['rows' => 6]) ?>
        <?= $form->field($model, 'statementFile')->widget(Upload::class, [
            'url' => ['file-storage/upload'],
            'maxFileSize' => 10 * 1024 * 1024,
            'multiple' => false,
            'acceptFileTypes' => new JsExpression('/(\.|\/)(pdf|doc|docx|jpg|jpeg|png|gif)$/i'),
        ]); ?>
        <div class="form-group">
            <?= Html::submitButton('Обновить', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
