<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\mailing\services\MailingBuilder
 *
 */

use dosamigos\ckeditor\CKEditor;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Новая рассылка';
$this->params['breadcrumbs'][] = ['label' => 'Рассылки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="directory-program-direction-create">
    <div class="directory-program-direction-form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'target')->checkboxList($model->targetsToSelect) ?>
        <?= $form->field($model, 'mun')->widget(Select2::class, [
            'data' => $model->munsToSelect,
            'options' => [
                'multiple' => true,
            ],
        ]) ?>
        <?= $form->field($model, 'message')->widget(CKEditor::className(), [
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
        <div class="form-group">
            <?= Html::submitButton(
                'Запустить рассылку',
                ['class' => 'btn btn-success']
            ) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
