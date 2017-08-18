<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\OrganizationContractSettings */
/* @var $organization \app\models\Organization */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
Modal::begin([
    'header' => '<strong>С указанными сведениями будет формироваться следующая шапка договора. Проставьте падежи и сохраните</strong>',
    'toggleButton' => [
        'tag' => 'a',
        'id' => 'open-document-form-modal',
        'label' => 'Созранить',
        'style' => 'display: none'
    ],
    'clientOptions' => ['backdrop' => 'static'],
]);
?>
<?php Pjax::begin(['id' => 'organization-document-form-modal']) ?>
<?php $form = ActiveForm::begin([
    'options' => [
        'data-pjax' => true
    ],
    'id' => 'organization-document-form'
]) ?>
    <div class="form-group-inline">
        <?php if (3 > $organization->type) : ?>
            <?php
            if ($organization->doc_type === 1) {
                $doc_type = 'доверенности от ' .
                    date('d.m.Y', strtotime($organization->date_proxy)) . ' № ' .
                    $organization->number_proxy;
            }
            if ($organization->doc_type === 2) {
                $doc_type = 'устава';
            }
            ?>
            <?= $organization->full_name . ', осуществляющ'; ?>
            <?= $form->field($model, 'organization_first_ending')->textInput(['style' => 'width:4em'])->label(false) ?>
            <?= ' образовательную  деятельность на основании лицензии от ' .
                date('d.m.Y', strtotime($organization->license_date)) . ' г. № ' .
                $organization->license_number . ', выданной ' . $organization->license_issued_dat . ', <br>именуем';
            ?>
            <?= $form->field($model, 'organization_second_ending')->textInput(['style' => 'width:4em'])->label(false) ?>
            <?= ' в дальнейшем "Исполнитель", в лице '. $organization->position .
                ' ' . $organization->name  .
                ', действующ'
            ?>
            <?= $form->field($model, 'director_name_ending')->textInput(['style' => 'width:4em'])->label(false) ?>
            <?= ' на основании ' ?>
            <?= $doc_type ?>
            <?= ', предлагает физическому лицу, являющемуся родителем (законным представителем) несовершеннолетнего, 
                включенного в систему персонифицированного финансирования дополнительного образования 
                на основании сертификата №0000000000, именуемого в дальнейшем "Обучающийся", именуемому в дальнейшем 
                "Заказчик" заключить Договор-оферту';
            ?>
        <?php elseif (3 === $organization->type) : ?>
            <?= $organization->full_name . ', осуществляющ'; ?>
            <?= $form->field($model, 'organization_first_ending')->textInput(['style' => 'width:4em'])->label(false) ?>
            <?= ' образовательную  деятельность на основании лицензии от ' .
                date('d.m.Y', strtotime($organization->license_date)) . ' г. № ' .
                $organization->license_number . ', выданной ' . $organization->license_issued_dat . ', <br>именуем';
            ?>
            <?= $form->field($model, 'organization_second_ending')->textInput(['style' => 'width:4em'])->label(false) ?>
            <?= ' в дальнейшем "Исполнитель", предлагает физическому лицу, являющемуся родителем (законным представителем) несовершеннолетнего, 
                включенного в систему персонифицированного финансирования дополнительного образования 
                на основании сертификата №0000000000, именуемого в дальнейшем "Обучающийся", именуемому в дальнейшем 
                "Заказчик" заключить Договор-оферту';
            ?>
        <?php elseif (4 === $organization->type) : ?>
            <?= $organization->full_name; ?>
            <?= ', <br>именуем' ?>
            <?= $form->field($model, 'organization_second_ending')->textInput(['style' => 'width:4em'])->label(false) ?>
            <?= ' в дальнейшем "Исполнитель", предлагает физическому лицу, являющемуся родителем 
                (законным представителем) несовершеннолетнего, 
                включенного в систему персонифицированного финансирования дополнительного образования 
                на основании сертификата №0000000000, именуемого в дальнейшем "Обучающийся", именуемому в дальнейшем 
                "Заказчик" заключить Договор-оферту';
            ?>
        <?php endif; ?>
    </div>
    <hr>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>
<?php $form::end() ?>
<?php Pjax::end(); ?>
<?php Modal::end() ?>
