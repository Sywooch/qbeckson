<?php
use app\models\DirectoryOrganizationForm;
use app\models\Organization;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use yii\widgets\Pjax;

/* @var $this yii\web\View */

$this->title = 'Сведения об организации';
$this->params['breadcrumbs'][] = $this->title;

$js = <<<'JS'
$('body').on('click', '#submit-organization-form', function(e) {
    e.preventDefault();
    $('form#organization-form').submit();
});
$("#organization-form-pjax").on("pjax:end", function() {
    $('#open-document-form-modal').click();
    selectType($('#organization-doc_type').val());
});
JS;
$this->registerJs($js, $this::POS_READY);
?>
<div class="col-md-10 col-md-offset-1">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-<?= $organization->type === Organization::TYPE_IP_WITHOUT_WORKERS ? '12' : '4' ?> well">
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
                <p><label class="control-label"><?= $organization->attributeLabels()['korr_invoice'] ?></label>
                    - <?= $organization['korr_invoice'] ?></p>
                <p><label class="control-label">Город банка</label> - <?= $organization['bank_sity'] ?></p>
                <p><label class="control-label">Контактное лицо</label> - <?= $organization['fio_contact'] ?></p>
                <?php if ($organization->type === Organization::TYPE_IP_WITHOUT_WORKERS):?>
                <p class="pull-right">
                    <a id="open-document-form-modal-0" data-toggle="modal" data-target="#w0" class="btn btn-primary">Создать шапку для договоров-оферт</a>
                </p>
                <?php endif; ?>
                <p>
                    <?= Html::a('Редактировать', ['/organization/edit', 'id' => $organization['id']], ['class' => 'btn btn-success']) ?>
                </p>
            </div>
            <?php Pjax::begin([
                'id' => 'organization-form-pjax'
            ]); ?>
            <div class="col-md-8">
                <?php
                if ($organization->type !== Organization::TYPE_IP_WITHOUT_WORKERS) {
                    $form = ActiveForm::begin([
                        'id' => 'organization-form',
                        'options' => [
                            'data-pjax' => true
                        ]
                    ]);
                    $doc_type = 'none';
                    if ($organization->doc_type === 1) {
                        $doc_type = 'block';
                    }
                    $doc_types = 'none';
                    if ($organization->doc_type === 3) {
                        $doc_types = 'block';
                    }
                    echo $form->errorSummary($organization);
                    echo '
                    <div class="well">
                    <h3 class="text-center">Для договора</h3>
                    <div class="form-group field-organization-license">
                        <label class="control-label" for="organization-type">
                        Сведения о лицензии от ' . date('m.d.Y', strtotime($organization->license_date)) .
                        ' №' . $organization->license_number . ':</label>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12 license">
                                    <p>выдана</p><div style="width: 100%">' .
                        $form->field($organization, 'license_issued_dat', ['template' => "{label}\n{hint}\n{input}\n<small>(в творительном падеже)</small>\n{error}"])->textInput(['maxlength' => true])->label(false)
                        . '</div>
                                </div>
                            </div>
                        </div>
                    </div>';
                    echo $form->field($organization, 'fio', ['template' => "{label}\n{hint}\n{input}\n<small>(в родительном падеже)</small>\n{error}"])->textInput(['maxlength' => true]);
                    echo $form->field($organization, 'position_min', ['template' => "{label}\n{hint}\n{input}\n<small>(кратко)</small>\n{error}"])->textInput(['maxlength' => true])->label('Должность представителя поставщика');
                    echo $form->field($organization, 'position', ['template' => "{label}\n{hint}\n{input}\n<small>(в родительном падеже)</small>\n{error}"])->textInput(['maxlength' => true]);

                    if ($organization->type === Organization::TYPE_IP_WITH_WORKERS) {
                    } else {
                        echo $form->field($organization, 'doc_type')->dropDownList([1 => 'Доверенности', 2 => 'Устава'], ['onChange' => 'selectType(this.value);']);
                        echo '<div class="row" id="proxy" style="display: ' . $doc_type . '">
                           <div class="col-md-6">' .
                            $form->field($organization, 'date_proxy')->widget(DateControl::class, [
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
                        echo $form->field($organization, 'organizational_form')
                            ->dropDownList(
                                ArrayHelper::map(DirectoryOrganizationForm::getList(), 'id', 'name'),
                                ['prompt' => 'Выберите..', 'options' => [5 => ['disabled' => true]]]
                            );
                    }
                    ActiveForm::end();
                    echo Html::a('Сохранить', ['#'], [
                        'class' => 'btn btn-primary',
                        'id' => 'submit-organization-form',
                    ]);
                    echo '</div>';
                }
                ?>
            </div>
            <?= $this->render('../organization/contract-settings/change-settings', [
                'model' => $organizationSettings,
            ]); ?>
            <?php Pjax::end(); ?>
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