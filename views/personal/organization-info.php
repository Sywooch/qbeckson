<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */

$this->title = 'Сведения об организации';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-md-10 col-md-offset-1">
    <div class="container-fluid" ng-app>
        <div class="row">
            <div class="col-md-<?= $organization->type == \app\models\Organization::TYPE_IP_WITHOUT_WORKERS ? "12" : "4" ?> well">
                <p><label class="control-label">Наименование организации</label> - <?= $organization['name'] ?></p>
                <p><label class="control-label">ИНН</label> - <?= $organization['inn'] ?></p>
                <p><label class="control-label">КПП</label> - <?= $organization['KPP'] ?></p>
                <p><label class="control-label">ОГРН</label> - <?= $organization['OGRN'] ?></p>
                <p><label class="control-label">ОКПО</label> - <?= $organization['okopo'] ?></p>
                <p><label class="control-label">Юридический адрес</label> - <?= $organization['address_legal'] ?></p>
                <p><label class="control-label">Фактический адрес</label> - <?= $organization['address_actual'] ?></p>
                <p><label class="control-label">Наименование банка</label> - <?= $organization['bank_name'] ?></p>
                <p><label class="control-label">Расчетный счет банка</label> - <?= $organization['rass_invoice'] ?></p>
                <p><label class="control-label">БИК Банка</label> - <?= $organization['bank_bik'] ?></p>
                <p><label class="control-label">Корр/Счет</label> - <?= $organization['korr_invoice'] ?></p>
                <p><label class="control-label">Город банка</label> - <?= $organization['bank_sity'] ?></p>
                <p><label class="control-label">Контактное лицо</label> - <?= $organization['fio_contact'] ?></p>
                <p>
                    <?= Html::a('Редактировать', ['/organization/edit', 'id' => $organization['id']], ['class' => 'btn btn-success']) ?>
                </p>
            </div>
            <div class="col-md-8">
                <?php $form = ActiveForm::begin(); ?>


                <?php
                if ($organization->type != 4) {
                    if ($organization->doc_type == 1) {
                        $doc_type = 'block';
                    } else {
                        $doc_type = 'none';
                    }
                    if ($organization->doc_type == 3) {
                        $doc_types = 'block';
                    } else {
                        $doc_types = 'none';
                    }
                    echo '
                   <div class="well">
                   <h3 class="text-center">Для договора</h3>
                   <div class="form-group field-organization-license">
                        <label class="control-label" for="organization-type">Сведения о лицензии</label>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-3 license">
                                    <p>(от </p>' .
                        $form->field($organization, 'license_date')->textInput(['readOnly' => true])->label(false)
                        . '</div>
                                <div class="col-md-3 license">
                                    <p>№</p>' .
                        $form->field($organization, 'license_number')->textInput(['readOnly' => true])->label(false)
                        . '</div>
                                <div class="col-md-6 license">
                                    <p>,&nbsp;выдана</p>' .
                        $form->field($organization, 'license_issued_dat', ['template' => "{label}\n{hint}\n{input}\n<small>(в творительном падеже)</small>\n{error}"])->textInput(['maxlength' => true])->label(false)
                        . '<p>).</p>
                                </div>
                            </div>
                        </div>
                   </div>';


                    echo $form->field($organization, 'fio', ['template' => "{label}\n{hint}\n{input}\n<small>(в родительном падеже)</small>\n{error}"])->textInput(['maxlength' => true]);

                    echo $form->field($organization, 'position_min', ['template' => "{label}\n{hint}\n{input}\n<small>(кратко)</small>\n{error}"])->textInput(['maxlength' => true])->label('Должность представителя организации');

                    echo $form->field($organization, 'position', ['template' => "{label}\n{hint}\n{input}\n<small>(в родительном падеже)</small>\n{error}"])->textInput(['maxlength' => true]);

                    if ($organization->type == \app\models\Organization::TYPE_IP_WITH_WORKERS) {

                        echo $form->field($organization, 'doc_type')->dropDownList([1 => 'Доверенности', 3 => 'Cвидетельства о государственной регистрации'], ['onChange' => 'selectType(this.value);']);

                        echo '<div id="svidet" style="display: ' . $doc_types . '">' .
                            $form->field($organization, 'svidet')->textInput(['readOnly' => true])
                            . '</div>';

                        echo '<div class="row" id="proxy" style="display: ' . $doc_type . '">
                           <div class="col-md-6">' .
                            $form->field($organization, 'date_proxy')->widget(DateControl::classname(), [
                                'type' => DateControl::FORMAT_DATE,
                                'ajaxConversion' => false,
                                'options' => [
                                    'pluginOptions' => [
                                        'autoclose' => true
                                    ]
                                ]
                            ])
                            . '</div>
                           <div class="col-md-6">' .
                            $form->field($organization, 'number_proxy')->textInput(['id' => 'number_proxy', 'maxlength' => true])
                            . '</div>
                        </div>';

                    } else {
                        echo $form->field($organization, 'doc_type')->dropDownList([1 => 'Доверенности', 2 => 'Устава'], ['onChange' => 'selectType(this.value);']);

                        echo '<div class="row" id="proxy" style="display: ' . $doc_type . '">
                           <div class="col-md-6">' .
                            $form->field($organization, 'date_proxy')->widget(DateControl::classname(), [
                                'type' => DateControl::FORMAT_DATE,
                                'ajaxConversion' => false,
                                'options' => [
                                    'pluginOptions' => [
                                        'autoclose' => true
                                    ]
                                ]
                            ])
                            . '</div>
                           <div class="col-md-6">' .
                            $form->field($organization, 'number_proxy')->textInput(['id' => 'number_proxy', 'maxlength' => true])
                            . '</div>
                        </div>';
                    }
                    if (empty($organization->organizational_form)) {
                        echo $form->field($organization, 'organizational_form')->dropDownList(ArrayHelper::map(app\models\DirectoryOrganizationForm::getList(), 'id', 'name'), ['prompt' => 'Выберите..', 'options' => [5 => ['disabled' => true]]]);
                    }

                    echo '<div class="form-group">' .
                        Html::submitButton('Сохранить', ['class' => $organization->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
                        . '</div>
                    </div>';
                }
                ?>


                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($organization, 'about')->textarea(['rows' => 6]) ?>

                <div class="form-group">
                    <?= Html::submitButton($organization->isNewRecord ? 'Добавить "Почему выбирают нас"' : 'Сохранить "Почему выбирают нас"', ['class' => $organization->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>