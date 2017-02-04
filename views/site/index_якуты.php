<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>

<div class="container-fluid">
    <div class="row header">
        <div class="col-md-2">
            <img class="logo" src="/img/logo.png" alt="logo">
        </div>
        <div class="col-md-8">
            <h1 class="title1">Портал сопровождения персонифицированного<br>
                            финансирования дополнительного образования детей<br>
                            Республики Саха (Якутия)</h1>
        </div>
        <div class="col-md-2">
            <?php $form = ActiveForm::begin([
                'id' => 'index-form',
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "<div class=\"col-lg-9\">{input}\n{error}</div>",
                ],
            ]); ?>

                <?= $form->field($model, 'username')->textInput() ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <div class="form-group">
                    <div class="col-lg-12">
                        <?= Html::submitButton('Вход', ['class' => 'btn btn-primary full login_btn', 'name' => 'login-button']) ?>
                    </div>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="row top_line">
        <div class="col-xs-offset-2 col-xs-8">
            <img src="/img/line2.png" alt="line">
            <a href="/programs/index">Поиск программ</a>
            <img src="/img/line2.png" alt="line">
            <a href="/site/about">Справочный раздел</a>
            <img src="/img/line2.png" alt="line">
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row bg">
        <div class="col-xs-12 image" id="bgimage">
            <div class="row">
                <div class="col-xs-offset-10 col-xs-2 right_bar">
                    <ul>
                        <li>Информация</li>
                        <li>ссылка</li>
                        <li>ссылка</li>
                        <li>ссылка</li>
                        <li>ссылка</li>
                        <li>ссылка</li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-offset-2 col-xs-8 bottom">
                    <div class="row">
                        <div class="col-xs-5 sl"></div>
                        <div class="col-xs-5 col-xs-offset-1 sl"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-5 sl"></div>
                        <div class="col-xs-5 col-xs-offset-1 sl"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-5 sl"></div>
                        <div class="col-xs-5 col-xs-offset-1 sl"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 bottom_line">
                    © My Company 2016 <a href="/site/contact">Контакты</a>
                </div>
            </div>
        </div>
    </div>
</div>

