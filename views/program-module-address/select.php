<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\forms\SelectModuleMainAddressForm */
/* @var $programModuleModel \app\models\ProgrammeModule */

$this->title = 'Изменить оснойной адрес';
$this->params['breadcrumbs'][] = [
    'label' => 'Программа',
    'url' => ['programs/view', 'id' => $programModuleModel->program_id]
];
$this->params['breadcrumbs'][] = [
    'label' => 'Редактирование адресов',
    'url' => ['program-module-address/update', 'moduleId' => $programModuleModel->id]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-module-address-create">
    <div class="program-module-address-form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'addressId')
            ->dropDownList(
                ArrayHelper::map($programModuleModel->addresses, 'id', 'address')
            ) ?>
        <div class="form-group">
            <?= Html::submitButton('Изменить', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
