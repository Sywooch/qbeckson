<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Mun;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Payers */
/* @var $form yii\widgets\ActiveForm */

$js = <<<'JS'
$('.item-checkbox').change(function() {
    var $this = $(this);
    var block = $this.closest('.form-group').next('.item-block');
    if($this.is(":checked")) {
        console.log('down');
        block.slideDown();
    } else {
        console.log('up');
        block.slideUp();
    }
})
JS;
$this->registerJs($js, $this::POS_READY);
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

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_dat')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'OGRN')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'INN')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'KPP')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'OKPO')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address_legal')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address_actual')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>

    <?php if (isset($roles['operators'])) {
        echo $form->field($model, 'mun')->dropDownList(ArrayHelper::map(Mun::findAllRecords('id, name'), 'id', 'name'));
    } ?>

    <label class="control-label">Оплачивает направленности</label>

    <?php foreach ($model::directionalityAttributes() as $attribute => $name) : ?>
        <hr>
        <?php if (in_array($name, $model->directionality)) : ?>
            <?php $model->$attribute = 1; ?>
        <?php endif; ?>
            <div class="item">
                <?= $form->field($model, $attribute)->checkbox([
                    'class' => 'item-checkbox'
                ])->label($name) ?>
                <?php if (in_array($name, $model->directionality)) : ?>
                    <div class="item-block">
                <?php else : ?>
                    <div class="item-block" style="display: none;">
                <?php endif; ?>
                    <?= $form->field($model, $attribute . '_count')->textInput(); ?>
                    <small class="underlabel">
                        Оставьте поле пустым или введите 0, чтобы не ограничивать
                        количество детей в этой направленности.
                    </small>
                </div>
            </div>
    <?php endforeach; ?>
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
