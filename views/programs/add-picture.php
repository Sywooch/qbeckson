<?php

use trntv\filekit\widget\Upload;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = 'Редактировать картинку программе: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Программа', 'url' => ['programs/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-add-picture">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <?php $fileUploadAttributes = [
        'url' => ['file-storage/program-upload'],
        'maxFileSize' => 10 * 1024 * 1024,
        'multiple' => false,
        'acceptFileTypes' => new JsExpression('/(\.|\/)(jpg|jpeg|png|gif)$/i'),
    ] ?>
    <?= $form->field($model, 'programPhoto')->widget(Upload::class, $fileUploadAttributes); ?>
    <?= Html::submitButton($model->getPhoto() ? 'Обновить' : 'Добавить', ['class' => 'btn btn-success']); ?>
    <?php $form::end(); ?>
</div>
