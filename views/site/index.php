<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Портал сопровождения персонифицированного финансирования дополнительного образования детей';
?>
<div class="site-index">

    <div class="jumbotron">

        <p class="lead"><?= $this->title ?></p>
        
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                ]); ?>

                    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'password')->passwordInput() ?>

                    <div class="form-group">
                        <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

    </div>
</div>
