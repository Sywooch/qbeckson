<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Mun;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Payers */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payers-form" ng-app>

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
    if (isset($roles['operators'])) {
        echo '<div class="well">';

        if ($user->isNewRecord) {
            echo $form->field($user, 'username', ['enableAjaxValidation' => true])->textInput(['id' => 'login', 'maxlength' => true]);
            echo Html::button('Сгенерировать логин', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#login").val(Math.random().toString(36).slice(-8)); })();']);
            echo '<br><br>';
            echo $form->field($user, 'password')->textInput(['id' => 'pass']);
            echo Html::button('Сгенерировать пароль', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#pass").val(Math.random().toString(36).slice(-8)); })();']);

        } else {
            echo $form->field($user, 'newlogin')->checkbox(['value' => 1, 'ng-model' => 'newlogin']);
            echo '<div ng-show="newlogin">';
            echo $form->field($user, 'username', ['enableAjaxValidation' => true])->textInput(['id' => 'login', 'maxlength' => true]);
            echo Html::button('Сгенерировать логин', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#login").val(Math.random().toString(36).slice(-8)); })();']);
            echo '</div>';

            echo $form->field($user, 'newpass')->checkbox(['value' => 1, 'ng-model' => 'newpass']);
            echo '<div ng-show="newpass">';
            echo $form->field($user, 'password')->textInput(['id' => 'pass', 'value' => '']);
            echo Html::button('Сгенерировать пароль', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#pass").val(Math.random().toString(36).slice(-8)); })();']);
            echo '</div>';
        }
        echo '</div>';
    } ?>

    <?= $form->field($model, 'code')->textInput(!isset($roles['operators']) ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(!isset($roles['operators']) ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?= $form->field($model, 'name_dat')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'OGRN')->textInput(!isset($roles['operators']) ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?= $form->field($model, 'INN')->textInput(!isset($roles['operators']) ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?= $form->field($model, 'KPP')->textInput(!isset($roles['operators']) ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?= $form->field($model, 'OKPO')->textInput(!isset($roles['operators']) ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?= $form->field($model, 'address_legal')->textInput(!isset($roles['operators']) ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?= $form->field($model, 'address_actual')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>

    <?php if (isset($roles['operators'])) {
        echo $form->field($model, 'mun')->dropDownList(ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'));
    } ?>

    <label class="control-label">Оплачивает направленности</label>

    <?php
    if (empty($model->directionality_1rob)) {
        print $form->field($model, 'directionality_1rob')->checkbox(['value' => 'Техническая (робототехника)', 'ng-model' => 'ShowCheck12'])->label('Техническая (робототехника)');
        print '<div ng-show="ShowCheck12">';
        print $form->field($model, 'directionality_1rob_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    } else {
        print $form->field($model, 'directionality_1rob')->checkbox(['value' => 'Техническая (робототехника)', 'ng-model' => 'ShowCheck12', 'ng-checked' => '!ShowCheck12'])->label('Техническая (робототехника)');
        print '<div ng-hide="ShowCheck12">';
        print $form->field($model, 'directionality_1rob_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    }
    ?>

    <?php
    if (empty($model->directionality_1)) {
        print $form->field($model, 'directionality_1')->checkbox(['value' => 'Техническая (иная)', 'ng-model' => 'ShowCheck1'])->label('Техническая (иная)');
        print '<div ng-show="ShowCheck1">';
        print $form->field($model, 'directionality_1_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    } else {
        print $form->field($model, 'directionality_1')->checkbox(['value' => 'Техническая (иная)', 'ng-model' => 'ShowCheck1', 'ng-checked' => '!ShowCheck1'])->label('Техническая (иная)');
        print '<div ng-hide="ShowCheck1">';
        print $form->field($model, 'directionality_1_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    }
    ?>

    <?php
    if (empty($model->directionality_2)) {
        print $form->field($model, 'directionality_2')->checkbox(['value' => 'Естественнонаучная', 'ng-model' => 'ShowCheck2'])->label('Естественнонаучная');
        print '<div ng-show="ShowCheck2">';
        print $form->field($model, 'directionality_2_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    } else {
        print $form->field($model, 'directionality_2')->checkbox(['value' => 'Естественнонаучная', 'ng-model' => 'ShowCheck2', 'ng-checked' => '!ShowCheck2'])->label('Естественнонаучная');
        print '<div ng-hide="ShowCheck2">';
        print $form->field($model, 'directionality_2_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    }
    ?>

    <?php
    if (empty($model->directionality_3)) {
        print $form->field($model, 'directionality_3')->checkbox(['value' => 'Физкультурно-спортивная', 'ng-model' => 'ShowCheck3'])->label('Физкультурно-спортивная');
        print '<div ng-show="ShowCheck3">';
        print $form->field($model, 'directionality_3_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    } else {
        print $form->field($model, 'directionality_3')->checkbox(['value' => 'Физкультурно-спортивная', 'ng-model' => 'ShowCheck3', 'ng-checked' => '!ShowCheck3'])->label('Физкультурно-спортивная');
        print '<div ng-hide="ShowCheck3">';
        print $form->field($model, 'directionality_3_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    }
    ?>

    <?php
    if (empty($model->directionality_4)) {
        print $form->field($model, 'directionality_4')->checkbox(['value' => 'Художественная', 'ng-model' => 'ShowCheck4'])->label('Художественная');
        print '<div ng-show="ShowCheck4">';
        print $form->field($model, 'directionality_4_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    } else {
        print $form->field($model, 'directionality_4')->checkbox(['value' => 'Художественная', 'ng-model' => 'ShowCheck4', 'ng-checked' => '!ShowCheck4'])->label('Художественная');
        print '<div ng-hide="ShowCheck4">';
        print $form->field($model, 'directionality_4_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    }
    ?>

    <?php
    if (empty($model->directionality_5)) {
        print $form->field($model, 'directionality_5')->checkbox(['value' => 'Туристско-краеведческая', 'ng-model' => 'ShowCheck5'])->label('Туристско-краеведческая');
        print '<div ng-show="ShowCheck5">';
        print $form->field($model, 'directionality_5_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    } else {
        print $form->field($model, 'directionality_5')->checkbox(['value' => 'Туристско-краеведческая', 'ng-model' => 'ShowCheck5', 'ng-checked' => '!ShowCheck5'])->label('Туристско-краеведческая');
        print '<div ng-hide="ShowCheck5">';
        print $form->field($model, 'directionality_5_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    }
    ?>

    <?php
    if (empty($model->directionality_6)) {
        print $form->field($model, 'directionality_6')->checkbox(['value' => 'Социально-педагогическая', 'ng-model' => 'ShowCheck6'])->label('Социально-педагогическая');
        print '<div ng-show="ShowCheck6">';
        print $form->field($model, 'directionality_6_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    } else {
        print $form->field($model, 'directionality_6')->checkbox(['value' => 'Социально-педагогическая', 'ng-model' => 'ShowCheck6', 'ng-checked' => '!ShowCheck6'])->label('Социально-педагогическая');
        print '<div ng-hide="ShowCheck6">';
        print $form->field($model, 'directionality_6_count')->textInput();
        print '<small class="underlabel">Оставьте поле пустым или введите 0, чтобы не ограничивать количество детей в этой направленности.</small>
        </div>';
    }
    ?>

    <div class="form-group">
        <?php
        if (isset($roles['operators'])) {
            echo Html::a('Отменить', '/personal/operator-payers', ['class' => 'btn btn-danger']);
        }
        if (isset($roles['payer'])) {
            echo Html::a('Отменить', '/personal/payer-info', ['class' => 'btn btn-danger']);
        }
        ?>
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
