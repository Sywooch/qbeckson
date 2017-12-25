<?php

use dosamigos\ckeditor\CKEditor;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Help */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="help-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'body')->widget(CKEditor::className(), [
        'clientOptions' => [
            'rows' => 10,
            'height' => 300,
            'toolbarGroups' => [
                ['name' => 'clipboard', 'groups' => ['mode', 'undo', 'selection', 'clipboard', 'doctools']],
                ['name' => 'editing', 'groups' => ['tools', 'about']],

                ['name' => 'paragraph', 'groups' => ['templates', 'list', 'indent', 'align']],
                ['name' => 'insert'],

                ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
                ['name' => 'colors'],
                ['name' => 'links'],
                ['name' => 'others'],
            ],
            'removeButtons' => 'Smiley,Iframe'
        ],
        'preset' => 'custom'
    ]) ?>

    <?= $form->field($model, 'for_guest')->checkbox(['uncheck' => 0]) ?>

    <?= $form->field($model, 'applied_to')
        ->checkboxList(
            \yii\helpers\ArrayHelper::map(
                Yii::$app->authManager->roles,
                'name',
                'name'
            )
        ) ?>
    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Создать' : 'Сохранить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
