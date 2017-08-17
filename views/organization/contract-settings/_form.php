<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrganizationContractSettings */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="organization-contract-settings-form">

    <?php

        /*$form->field($model, 'change1')->textInput(['style' => 'width:4em'])->label(false)
        .' образовательную  деятельность на основании лицензии от '.$license_date[2].'.'.$license_date[1].'.'.$license_date[0].' г. № '.$organization->license_number.', выданной '.$organization->license_issued_dat.', <br>именуем'.
        $form->field($model, 'change2')->textInput(['style' => 'width:4em'])->label(false)
        .' в дальнейшем "Исполнитель", в лице '.$model->org_position.' '.
        $form->field($model, 'change_org_fio')->textInput(['style' => 'width:20em'])->label(false)
        .', действующ'.
        $form->field($model, 'change10')->textInput(['style' => 'width:4em'])->label(false)
        .' на основании '.
        $form->field($model, 'change_doctype')->textInput(['style' => 'width:20em'])->label(false)*/

    ?>

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'organization_id')->textInput() ?>
    <?= $form->field($model, 'organization_first_ending')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'organization_second_ending')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'director_name_ending')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'document_type')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
