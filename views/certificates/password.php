<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Certificates */

$this->title = 'Изменить пароль';

$this->params['breadcrumbs'][] = ['label' => 'Сертификат', 'url' => ['/personal/certificate-info']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificates-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($user, 'oldpassword')->passwordInput()?>

    <?= $form->field($user, 'newpassword')->passwordInput()?>
           
    <?= $form->field($user, 'confirm')->passwordInput()->label('Подтвердите новый пароль') ?>
            
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>

</div>
