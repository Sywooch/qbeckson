<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\CertGroup;
use app\models\Payers;

/* @var $this yii\web\View */
/* @var $model app\models\Certificates */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="certificates-form" ng-app>

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
    if (isset($roles['payer'])) {
        echo '<div class="well">';

        if ($user->isNewRecord) {
            echo $form->field($user, 'username', ['enableAjaxValidation' => true, 'addon' => ['prepend' => ['content'=> $region . $payer->code]]])->textInput(['id' => 'login', 'maxlength' => true])->label('Номер сертификата');
            echo Html::button('Сгенерировать номер сертификата', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#login").val( Math.round(0 - 0.5 + Math.random() * (9 - 0 + 1)).toString()+Math.round(0 - 0.5 + Math.random() * (9 - 0 + 1)).toString()+Math.round(0 - 0.5 + Math.random() * (9 - 0 + 1)).toString()+Math.round(0 - 0.5 + Math.random() * (9 - 0 + 1)).toString()+Math.round(0 - 0.5 + Math.random() * (9 - 0 + 1)).toString()+Math.round(0 - 0.5 + Math.random() * (9 - 0 + 1)).toString() ); })();']);
            echo '<br><br>';
            echo $form->field($user, 'password')->textInput(['id' => 'pass']);
            echo Html::button('Сгенерировать пароль', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#pass").val(Math.random().toString(36).slice(-8)); })();']);

        }
        else {
            echo $form->field($user, 'newlogin')->checkbox(['value' => 1, 'ng-model' => 'newlogin', 'label' => 'Изменить номер сертификата']);
            echo '<div ng-show="newlogin">';
                echo $form->field($user, 'username', ['enableAjaxValidation' => true])->textInput(['id' => 'login', 'maxlength' => true])->label('Номер сертификата');
                /* echo Html::button('Сгенерировать номер сертификата', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#login").val(Math.random().toString(36).slice(-8)); })();']); */
            echo '</div>';

            echo $form->field($user, 'newpass')->checkbox(['value' => 1, 'ng-model' => 'newpass']);
            echo '<div ng-show="newpass">';
                echo $form->field($user, 'password')->textInput(['id' => 'pass', 'value' => '']);
                echo Html::button('Сгенерировать пароль', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#pass").val(Math.random().toString(36).slice(-8)); })();']);
            echo '</div>';
        }
        echo '</div>';
    } ?>

    <div class="panel panel-default">
        <div class="panel-heading">ФИО ребенка</div>
        <div class="panel-body">
            <?= $form->field($model, 'soname')->textInput(!isset($roles['payer']) ? ['readOnly'=>true] : ['maxlength' => true]) ?>
            
            <?= $form->field($model, 'name')->textInput(!isset($roles['payer']) ? ['readOnly'=>true] : ['maxlength' => true]) ?>

            <?= $form->field($model, 'phname')->textInput(!isset($roles['payer']) ? ['readOnly'=>true] : ['maxlength' => true]) ?>
        </div>
    </div>

    <?= $form->field($model, 'fio_parent')->textInput(['maxlength' => true]) ?>

    <?php
    if (isset($roles['payer'])) {
        $payer = Yii::$app->user->identity->payer;
        $groupList = CertGroup::getActiveList($payer->id);
        $dataOptions = ArrayHelper::map($groupList, 'id', 'nominal');
        foreach ($dataOptions as $index => $value) {
            $dataOptions[$index] = ['data-nominal' => $value];
        }

<<<<<<< Updated upstream
        echo $form->field($model, 'cert_group')->dropDownList(ArrayHelper::map($groupList, 'id', 'group'), ['options' => $dataOptions, 'onChange' => 'selectGroup(this);', 'prompt' => 'Выберите группу...']);
=======
        echo $form->field($model, 'cert_group')->dropDownList(ArrayHelper::map($groupList, 'id', 'group'), ['options' => $dataOptions, 'onChange' => 'selectGroup(this);', 'prompt' => 'Выберите группу...'])->label('Текущая группа сертификата');
        echo $form->field($model, 'possible_cert_group')->dropDownList(ArrayHelper::map(CertGroup::getPossibleList($payer->id), 'id', 'group'), ['prompt' => 'Выберите группу...'])->label('Возможная группа сертификата');
    } elseif (Yii::$app->user->can('certificate') && $model->canChangeGroup) {
        echo $form->field($model, 'cert_group')->dropDownList(ArrayHelper::map($model->possibleGroupList, 'id', 'group'), ['prompt' => 'Выберите группу...']);
>>>>>>> Stashed changes
    }
    ?>

    <?= $form->field($model, 'nominal')->textInput(!isset($roles['payer']) ? ['readOnly'=>true] : ['maxlength' => true, 'id' => 'nominalField']) ?>
    
     <?php if (!$model->isNewRecord) { 
          echo  $form->field($model, 'balance')->textInput(!isset($roles['payer']) ? ['readOnly'=>true] : ['maxlength' => true, 'id' => 'nom']);
    } ?>

    <div class="form-group">
       <?php
        if (isset($roles['certificate'])) {
            echo Html::a('Отменить', '/personal/certificate-statistic', ['class' => 'btn btn-danger']);
        }
        if (isset($roles['payer'])) {
            echo Html::a('Отменить', '/personal/payer-certificates', ['class' => 'btn btn-danger']);
        }
        ?>    
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
