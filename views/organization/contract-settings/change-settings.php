<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\forms\OrganizationSettingsForm */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
Modal::begin([
    'header' => '<strong>С указанными сведениями будет формироваться следующая шапка договора. Проставьте падежи и сохраните</strong>',
    'toggleButton' => [
        'tag' => 'a',
        'id' => 'open-document-form-modal',
        'label' => 'Сохранить',
        'style' => 'display: none;'
    ],
]);
?>
    <?php $form = ActiveForm::begin([
        'options' => [
            'data-pjax' => true
        ],
        'id' => 'organization-document-form'
    ]) ?>
        <div class="form-group-inline">
            <?php
            $text = $model->generateHeader();
            foreach ($model->getAttributes() as $key => $attribute) {
                $text = str_replace(
                    "{{{$key}}}",
                    $form->field($model, $key)->textInput(['style' => 'width:4em'])->label(false),
                    $text
                );
            }
            echo $text;
            ?>
        </div>
        <hr>
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
    <?php $form::end() ?>
<?php Modal::end() ?>
