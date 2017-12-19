<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Organization */

$this->title = 'Направить запрос на удаление договора';
$this->params['breadcrumbs'][] = ['label' => 'Список запросов на удаление', 'url' => ['contract']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-delete-application-create  col-md-10 col-md-offset-1">
    <div class="organization-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'contract_id')->textInput(['disabled' => true]) ?>

        <?= $form->field($model, 'reason')->textarea() ?>

        <?= $form->field($model, 'confirmationFile')->widget(\trntv\filekit\widget\Upload::className(), [
            'url' => ['file-storage/contract-delete-application'],
            'maxFileSize' => 3 * 1024 * 1024,
            'acceptFileTypes' => new \yii\web\JsExpression('/(\.|\/)(pdf|jpg|jpeg|gif|png)$/i'),
        ]); ?>
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']); ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
