<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $municipalities \app\models\Mun[] */

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
                <div class="panel panel-default">
                    <div class="panel-body">
                        <a href="javascript:void(0);" data-toggle="modal" data-target="#information-modal">
                            Узнать, как получить сертификат
                        </a>
                        <br>
                        <br>
                        <a href="javascript:void(0);" data-toggle="modal" data-target="#create-organization-modal">
                            Отправить заявку на регистрацию организации
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal 2 -->
<div class="modal fade" id="information-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть"><span aria-hidden="true">&times;</span></button>
                <h4>Узнать, как получить сертификат</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php foreach ($municipalities as $municipality) : ?>
                        <div class="col-md-6">
                            <a href="<?= Url::to(['site/information', 'municipalityId' => $municipality->id]) ?>">
                                <?= $municipality->name ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1 -->
<div class="modal fade" id="create-organization-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть"><span aria-hidden="true">&times;</span></button>
                <h4>Заявка на регистрацию организации</h4>
            </div>
            <div class="modal-body">
                <a href="<?= Url::to('/organization/request') ?>" class="btn btn-primary">Отправить заявку на регистрацию организации</a><br /><br />
                <a href="<?= Url::to('/organization/check-status') ?>" class="btn btn-success">Проверить статус заявки</a>
            </div>
        </div>
    </div>
</div>