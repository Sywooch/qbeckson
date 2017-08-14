<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $module \app\models\ProgrammeModule */
/* @var $model \app\models\forms\ModuleAddressForm */

$this->title = 'Редактирование адресов модуля';
$this->params['breadcrumbs'][] = ['label' => 'Программа', 'url' => ['programs/view', 'id' => $module->program_id]];
$this->params['breadcrumbs'][] = $this->title;

$js = <<<'JS'
    $(document).on('change', '.address-checkbox', function () {
        var $this = $(this);
        var boxes = $('.address-checkbox');
        if($this.is(":checked")) {
            boxes.prop('checked', false);
            $this.prop('checked', true);
        }
    });
JS;
$this->registerJs($js, $this::POS_READY);
?>
<div class="programs-add-picture">
    <?php if ([] === $model->getModel()->program->addresses) : ?>
        <p class="lead">Необходимо добавить адресы для программы</p>
    <?php else : ?>
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td>Основной адрес</td>
                            <td>Адрес</td>
                            <td>Выбрать</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($model->addressIds as $key => $address) : ?>
                            <tr>
                                <td>
                                    <?= $form->field($model, "statuses[{$key}]")
                                        ->checkbox([
                                            'label' => '',
                                            'class' => 'address-checkbox'
                                        ]) ?>
                                </td>
                                <td>
                                    <?= $model->names[$key] ?>
                                </td>
                                <td>
                                    <?= $form->field($model, "isChecked[{$key}]")
                                        ->checkbox(['label' => '']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']); ?>
        <?php $form::end(); ?>
    <?php endif; ?>
</div>

