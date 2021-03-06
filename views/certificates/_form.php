<?php

use app\models\CertGroup;
use kartik\dialog\Dialog;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Certificates */
/* @var $form yii\widgets\ActiveForm */

echo Dialog::widget(['options' => ['title' => 'Предупреждение',]]);

?>
    <div class="certificates-form" ng-app>
        <?php $form = ActiveForm::begin(['enableAjaxValidation' => true]); ?>
        <?php
        $formId = $form->getId();
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        if (isset($roles['payer'])) {

            if ($user->isNewRecord) {
                echo '<div class="well">';
                echo $form->field($user, 'username', ['enableAjaxValidation' => true, 'addon' => ['prepend' => ['content' => $region . $payer->code]]])->textInput(['id' => 'login', 'maxlength' => true])->label('Номер сертификата');
                echo Html::button('Сгенерировать номер сертификата', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#login").val( Math.round(0 - 0.5 + Math.random() * (9 - 0 + 1)).toString()+Math.round(0 - 0.5 + Math.random() * (9 - 0 + 1)).toString()+Math.round(0 - 0.5 + Math.random() * (9 - 0 + 1)).toString()+Math.round(0 - 0.5 + Math.random() * (9 - 0 + 1)).toString()+Math.round(0 - 0.5 + Math.random() * (9 - 0 + 1)).toString()+Math.round(0 - 0.5 + Math.random() * (9 - 0 + 1)).toString() ); })();']);
                echo '<br><br>';
                echo $form->field($user, 'password')->textInput(['id' => 'pass']);
                echo Html::button('Сгенерировать пароль', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#pass").val(Math.random().toString(36).slice(-8)); })();']);
                echo '</div>';
            } else {
                echo '<div class="well">';
                if ($model->canChangeGroup) {
                    echo $form->field($user, 'newlogin')->checkbox(['value' => 1, 'ng-model' => 'newlogin', 'label' => 'Изменить номер сертификата']);
                    echo '<div ng-show="newlogin">';
                    echo $form->field($user, 'username', ['enableAjaxValidation' => true])->textInput(['id' => 'login', 'maxlength' => true])->label('Номер сертификата');
                    echo '</div>';
                }

                echo $form->field($user, 'newpass')->checkbox(['value' => 1, 'ng-model' => 'newpass']);
                echo '<div ng-show="newpass">';
                echo $form->field($user, 'password')->textInput(['id' => 'pass', 'value' => '']);
                echo Html::button('Сгенерировать пароль', ['class' => 'btn btn-success', 'onclick' => '(function () { $("#pass").val(Math.random().toString(36).slice(-8)); })();']);
                echo '</div>';
                echo '</div>';
            }
        } ?>

        <div class="panel panel-default">
            <div class="panel-heading">ФИО ребенка</div>
            <div class="panel-body">
                <?= $form->field($model, 'soname')->textInput(!isset($roles['payer']) ? ['readOnly' => true] : ['maxlength' => true]) ?>

                <?= $form->field($model, 'name')->textInput(!isset($roles['payer']) ? ['readOnly' => true] : ['maxlength' => true]) ?>

                <?= $form->field($model, 'phname')->textInput(!isset($roles['payer']) ? ['readOnly' => true] : ['maxlength' => true]) ?>
            </div>
        </div>

        <?= $form->field($model, 'fio_parent')->textInput(['maxlength' => true]) ?>

        <?php
        if (isset($roles['payer']) && $model->canChangeGroup) {
            $payer = Yii::$app->user->identity->payer;
            $groupList = CertGroup::getActiveList($payer->id);
            $dataOptions = ArrayHelper::map($groupList, 'id', 'nominal');
            foreach ($dataOptions as $index => $value) {
                $dataOptions[$index] = ['data-nominal' => $value];
            }

            echo $form->field($model, 'possible_cert_group')->dropDownList(ArrayHelper::map(CertGroup::getPossibleList($payer->id), 'id', 'group'), [
                'options' => $dataOptions,
                'prompt' => 'Выберите группу...',
                'onChange' => 'selectGroup($("#select-cert-group")); ',
                'id' => 'possible-cert-group'
            ]);

            echo $form->field($model, 'selectCertGroup')->radioList($model->getCertGroupTypes(), [
                'item' => function ($index, $label, $name, $checked, $value) use ($dataOptions)
                {
                    $return = '<div class="radio"><label><input type="radio" name="' . $name . '" value="' . $value . '" data-force-nominal="' . ($value == \app\models\Certificates::TYPE_ACCOUNTING ? 0 : 1) . '" data-index="' . $index . '" ' . ($checked ? 'checked' : '') . '> ' . $label . '</label></div>';

                    return $return;
                },
                'onClick' => 'return selectGroup(this);',
                'id' => 'select-cert-group',
            ]);
        }
        ?>

        <?php echo $form->field($model, 'nominal')->textInput(['readOnly' => true, 'maxlength' => true, 'id' => 'nominalField']); ?>

        <div class="form-group">
            <?php
            if (isset($roles['certificate'])) {
                echo Html::a('Отменить', '/personal/certificate-statistic', ['class' => 'btn btn-danger']);
            }
            if (isset($roles['payer'])) {
                echo Html::a('Отменить', '/personal/payer-certificates', ['class' => 'btn btn-danger']);
            }
            ?>
            <?php echo Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']); ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php
$js = <<<JS

const form = $('#$formId');

$("#possible-cert-group").on('change', function() {
   form.yiiActiveForm('validate', true);
});

const t = function() {
  setTimeout(function() {
      $("#select-cert-group").on('click', t);
      form.yiiActiveForm('validate', true);
    }, 1);
  $("#select-cert-group").off('click', t);
};
$("#select-cert-group").on('click', t);

JS;
$this->registerJs($js);
?>