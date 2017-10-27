<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\mailing\services\MailingBuilder
 *
 */

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
        <?= $form->field($model, 'message')->textarea(['rows' => 10]) ?>
        <div class="form-group">
            <?= Html::submitButton(
                'Запустить рассылку',
                ['class' => 'btn btn-success']
            ) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
