<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datecontrol\DateControl;
use app\models\Mun;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Organization */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="organization-form" ng-app>

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

    <?= $form->field($model, 'name')->textInput(!isset($roles['operators']) ? ['readOnly' => true] : ['maxlength' => true])->label('Сокращенное наименование организации') ?>

    <?= $form->field($model, 'full_name')->textInput(!isset($roles['operators']) ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?php if (isset($roles['operators'])) {
        echo $form->field($model, 'type')->dropDownList([1 => 'Образовательная организация', 2 => 'Организация, осуществляющая обучение', 3 => 'Индивидуальный предприниматель, оказывающий услуги с наймом работников', 4 => 'Индивидуальный предприниматель, оказывающий услуги без найма работников'], ['onChange' => 'selectTypes(this.value);']);


        if (isset($model->type)) {
            if ($model->type == 1 or $model->type == 2) {
                $display = 'block';
                $svid = 'none';
            }

            if ($model->type == 4) {
                $display = 'none';
                $svid = 'block';
            }

            if ($model->type == 3) {
                $display = 'block';
                $svid = 'block';
            }
        } else {
            $display = 'block';
            $svid = 'none';
        }

        echo '<div id="proxy" style="display: ' . $display . '" class="form-group field-organization-license well">
            <label class="control-label" for="organization-type">Сведения о лицензии</label>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3 license">
                        <p>(от </p>';
        echo $form->field($model, 'license_date')->widget(DateControl::classname(), [
            'type' => DateControl::FORMAT_DATE,
            'ajaxConversion' => false,
            'options' => [
                'pluginOptions' => [
                    'autoclose' => true
                ]
            ]
        ])->label(false);
        echo '</div>
                    <div class="col-md-3 license">
                        <p>№</p>';
        echo $form->field($model, 'license_number')->textInput()->label(false);
        echo '</div>
                    <div class="col-md-6 license">
                        <p>,&nbsp;выдана</p>';
        echo $form->field($model, 'license_issued')->textInput(['maxlength' => true])->label(false);
        echo '<p>).</p>
                    </div>
                </div>
            </div>
        </div>';

        echo '<div id="svid" style="display: ' . $svid . '" class="form-group field-organization-svid well">';
        echo $form->field($model, 'svidet')->textInput();
        echo '</div>';

    } ?>

    <div class="well">
        <small>Банковские реквизиты</small>
        <br><br>

        <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'bank_sity')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'bank_bik')->textInput() ?>

        <?= $form->field($model, 'korr_invoice')->textInput() ?>

        <?= $form->field($model, 'rass_invoice')->textInput(['maxlength' => true]) ?>
    </div>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'site')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fio_contact')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address_actual')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address_legal')->textInput(!isset($roles['operators']) ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?= $form->field($model, 'inn')->textInput(!isset($roles['operators']) ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?= $form->field($model, 'KPP')->textInput(!isset($roles['operators']) ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?php
    if ($model->type == 3 or $model->type == 4) {
        $ogrn = 'ОГРНИП';
    } else {
        $ogrn = 'ОГРН';
    }
    ?>

    <?= $form->field($model, 'OGRN')->textInput(!isset($roles['operators']) ? ['readOnly' => true] : ['maxlength' => true])->label($ogrn); ?>

    <?= $form->field($model, 'last')->textInput(['maxlength' => true]) ?>


    <?php if (isset($roles['operators'])) {
        if (!$model->isNewRecord) {
            echo $form->field($model, 'last_year_contract')->textInput();
            echo $form->field($model, 'cratedate')->textInput();
        }
        echo $form->field($model, 'mun')->dropDownList(ArrayHelper::map(Mun::find()->all(), 'id', 'name'));
    } ?>

    <div class="form-group">
        <?php
        if (isset($roles['operators'])) {
            echo Html::a('Отменить', '/personal/operator-organizations', ['class' => 'btn btn-danger']);
        }
        if (isset($roles['organizations'])) {
            echo Html::a('Отменить', '/personal/organization-info', ['class' => 'btn btn-danger']);
        }
        ?>
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
