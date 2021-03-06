<?php

use trntv\filekit\widget\Upload;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datecontrol\DateControl;
use app\models\Mun;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Organization */
/* @var $form yii\widgets\ActiveForm */

$readonlyField = !Yii::$app->user->can('operators') && !Yii::$app->user->isGuest;
?>

<div class="organization-form" ng-app>

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (Yii::$app->user->can('operators')) {
        echo '<div class="well">';

        if ($user->isNewRecord || $model->isModerating) {
            echo $form->field($user, 'username', ['enableAjaxValidation' => true])->textInput(['id' => 'login', 'maxlength' => true]);
            echo Html::button('Сгенерировать логин', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#login").val(Math.random().toString(36).slice(-8)); })();']);
            echo '<br><br>';
            echo $form->field($user, 'password')->textInput(['id' => 'pass', 'value' => '']);
            echo Html::button('Сгенерировать пароль', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#pass").val(Math.random().toString(36).slice(-8)); })();']);

        } else {
            echo $form->field($user, 'newlogin')->checkbox(['value' => 1, 'ng-model' => 'newlogin']);
            echo '<div ng-show="newlogin">';
            echo $form->field($user, 'username', ['enableAjaxValidation' => true])->textInput(['id' => 'login', 'maxlength' => true]);
            echo Html::button('Сгенерировать логин', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#login").val(Math.random().toString(36).slice(-8)); })();']);
            echo '</div>';
            echo $form->field($user, 'newpass')->checkbox(['value' => 1, 'ng-model' => 'newpass', 'ng-init' => 'checked=1']);
            echo '<div ng-show="newpass">';
            echo $form->field($user, 'password')->textInput(['id' => 'pass', 'value' => '']);
            echo Html::button('Сгенерировать пароль', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#pass").val(Math.random().toString(36).slice(-8)); })();']);
            echo '</div>';
        }
        echo '</div>';
    } ?>

    <?= $form->field($model, 'name')->textInput($readonlyField ? ['readOnly' => true] : ['maxlength' => true])->label('Сокращенное наименование поставщика') ?>

    <?= $form->field($model, 'full_name')->textInput($readonlyField ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?php if (empty($model->organizational_form)) {
        $flagOrgForm = true;
        echo $form->field($model, 'organizational_form')
            ->dropDownList(
                ArrayHelper::map(\app\models\DirectoryOrganizationForm::getList(), 'id', 'name'),
                ['prompt' => 'Выберите..', 'options' => [5 => ['disabled' => true]]]
            );
    } ?>

    <?php if (!$readonlyField) {
        if (empty($flagOrgForm)) {
            echo $form->field($model, 'organizational_form')->dropDownList(ArrayHelper::map(app\models\DirectoryOrganizationForm::getList(), 'id', 'name'), ['prompt' => 'Выберите..', 'options' => [5 => ['disabled' => true]]]);
        }

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

        <div class="invoices">
            <div class="checkbox-container">
                <?= Html::checkbox('correspondent_invoice_show', $model->correspondent_invoice ?: false, ['name' => 'asd', 'label' => 'Указать корреспондентский счёт (к/с)', 'onClick' => 'showNextContainer(this); $(".correspondent_invoice input").val("")']) ?>
            </div>

            <div class="correspondent_invoice" <?= $model->correspondent_invoice ?: 'style="display: none"' ?>>
                <?= $form->field($model, 'correspondent_invoice')->textInput() ?>
            </div>
        </div>

        <?= $form->field($model, 'rass_invoice')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'receiver')->textInput(['maxlength' => true]) ?>
    </div>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'site')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fio_contact')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address_actual')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address_legal')->textInput($readonlyField ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?= $form->field($model, 'inn')->textInput($readonlyField ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?= $form->field($model, 'KPP')->textInput($readonlyField ? ['readOnly' => true] : ['maxlength' => true]) ?>

    <?php
    if ($model->type == 3 or $model->type == 4) {
        $ogrn = 'ОГРНИП';
    } else {
        $ogrn = 'ОГРН';
    }
    ?>

    <?= $form->field($model, 'OGRN')->textInput($readonlyField ? ['readOnly' => true] : ['maxlength' => true])->label($ogrn); ?>

    <?= $form->field($model, 'last')->textInput(['maxlength' => true]) ?>


    <?php if (!$readonlyField) {
        if (!$model->isNewRecord && !Yii::$app->user->isGuest) {
            echo $form->field($model, 'last_year_contract')->textInput();
            echo $form->field($model, 'cratedate')->textInput();
        }
        echo $form->field($model, 'mun')->dropDownList(ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'));
    } ?>

    <?php if (Yii::$app->user->isGuest || $model->isModerating): ?>
        <div class="well">
            <?php if (!empty($model->license)): ?>
                <?= Html::a('Лицензия (документ)', $model->license[0]->getUrl()) ?>
                <br>
            <? endif; ?>
            <?php if (!empty($model->charter)): ?>
                <?= Html::a('Устав (документ)', $model->charter[0]->getUrl()) ?>
                <br>
            <? endif; ?>
            <?php if (!empty($model->statement)): ?>
                <?= Html::a('Выписка из ЕГРЮЛ/ЕГРИП (документ)', $model->statement[0]->getUrl()) ?>
            <? endif; ?>
            <?php if (!empty($model->documents)): ?>
                <h4>Иные документы:</h4>
                <?php foreach ($model->documents as $i => $document): ?>
                    <?= Html::a('Документ ' . ($i + 1), $document->getUrl()) ?><br/>
                <? endforeach; ?>
            <? endif; ?>
        </div>
    <?php endif; ?>
    <?php if (Yii::$app->user->isGuest) : ?>
        <?php $fileUploadAttributes = [
            'url' => ['file-storage/upload'],
            'maxFileSize' => 10 * 1024 * 1024,
            'multiple' => true,
            'acceptFileTypes' => new JsExpression('/(\.|\/)(pdf|doc|docx)$/i'),
        ] ?>
        <?= $form->field($model, 'licenseDocument')->widget(Upload::class, $fileUploadAttributes); ?>
        <?= $form->field($model, 'charterDocument')->widget(Upload::class, $fileUploadAttributes); ?>
        <?= $form->field($model, 'statementDocument')->widget(Upload::class, $fileUploadAttributes); ?>
        <?= $form->field($model, 'commonDocuments')->widget(Upload::class, array_merge($fileUploadAttributes, [
            'maxNumberOfFiles' => 3,
        ])); ?>
        <?= $form->field($model, 'verifyCode')->widget(yii\captcha\Captcha::className()) ?>
    <?php endif; ?>

    <div class="form-group">
        <?php
        if (Yii::$app->user->can('operators') && !$model->isModerating) {
            echo Html::a('Отменить', '/personal/operator-organizations', ['class' => 'btn btn-danger']);
        } elseif (Yii::$app->user->can('organizations')) {
            echo Html::a('Отменить', '/personal/organization-info', ['class' => 'btn btn-danger']);
        }
        ?>
        <?php
        if (Yii::$app->user->isGuest) {
            echo Html::submitButton('Отправить заявку на подключение', ['class' => 'btn btn-success btn-lg']);
        } elseif ($model->isModerating) {
            echo Html::submitButton('Подтвердить заявку', ['class' => 'btn btn-success', 'name' => 'accept-button']);
            echo '&nbsp;&nbsp;' . Html::a('Отклонить заявку', 'javascript:void();', ['class' => 'btn btn-warning show-refuse-reason']);
            echo '<div class="container-refuse-reason"><br />';
            echo $form->field($model, 'refuse_reason')->textarea(['rows' => 6]);
            echo Html::submitButton('Отклонить заявку', ['class' => 'btn btn-warning', 'name' => 'refuse-button']) . '</div>';
        } else {
            echo Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
        } ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
