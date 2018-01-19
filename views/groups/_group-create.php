<?php

/**
 * часть страницы для создания группы
 *
 * @var $this View
 * @var $form ActiveForm
 * @var $groupCreator GroupCreator
 */

use app\models\groups\GroupCreator;
use kartik\datecontrol\DateControl;
use kartik\widgets\DepDrop;
use kartik\widgets\TimePicker;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

$js = <<<'JS'
    $('.show-form-options').change(function() {
        var $this = $(this);
        if($this.is(":checked")) {
            $this.closest('.form-checker').next('.form-options').slideDown();
        } else {
            $this.closest('.form-checker').next('.form-options').slideUp();
        }
    });
JS;
$this->registerJs($js, $this::POS_READY);
?>

<?php if ($groupCreator->needModuleSet): ?>
    <?= $form->field($groupCreator->group, 'program_id')
        ->dropDownList(
            $groupCreator->programList,
            ['id' => 'prog-id', 'prompt' => '-- Не выбрана --',]
        ) ?>

    <?= $form->field($groupCreator->group, 'year_id')->widget(DepDrop::class, [
        'options' => ['id' => 'module-id'],
        'pluginOptions' => [
            'depends' => ['prog-id'],
            'placeholder' => '-- Не выбран --',
            'url' => Url::to(['groups/year'])
        ]
    ])->label('Модуль') ?>
<?php endif; ?>

<?= $form->field($groupCreator->group, 'name')->textInput(['maxlength' => true]) ?>
<hr>
<?php foreach ($groupCreator->groupClasses as $i => $class) : ?>
    <div class="form-checker">
        <?= $form->field($groupCreator->groupClasses[$i], "[{$i}]status")
            ->checkbox([
                'label' => $class->week_day,
                'class' => 'show-form-options'
            ])->label(false); ?>
    </div>
    <div class="form-options" style="display: none">
        <div class="row">
            <div class="col-md-6">
                <?php if ($groupCreator->needModuleSet): ?>
                    <?= $form->field($groupCreator->groupClasses[$i], "[{$i}]address")
                        ->widget(DepDrop::class, [
                            'pluginOptions' => [
                                'depends' => ['module-id'],
                                'placeholder' => '-- Не выбран --',
                                'url' => Url::to(['groups/select-addresses'])
                            ]
                        ]) ?>
                <?php else: ?>
                    <?= $form->field($groupCreator->groupClasses[$i], "[{$i}]address")
                        ->dropDownList($groupCreator->programModuleAddresses) ?>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($groupCreator->groupClasses[$i], "[{$i}]classroom")
                    ->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($groupCreator->groupClasses[$i], "[{$i}]time_from")
                    ->widget(TimePicker::class, [
                        'pluginOptions' => [
                            'showMeridian' => false,
                            'minuteStep' => 5,
                        ]
                    ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($groupCreator->groupClasses[$i], "[{$i}]time_to")
                    ->widget(TimePicker::class, [
                        'pluginOptions' => [
                            'showMeridian' => false,
                            'minuteStep' => 5,
                        ]
                    ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($groupCreator->groupClasses[$i], "[{$i}]hours_count")
                    ->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <hr>
    </div>
<?php endforeach; ?>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($groupCreator->group, 'datestart')->widget(DateControl::class, [
            'type' => DateControl::FORMAT_DATE,
            'ajaxConversion' => false,
            'options' => [
                'pluginOptions' => [
                    'autoclose' => true
                ]
            ]
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($groupCreator->group, 'datestop')->widget(DateControl::class, [
            'type' => DateControl::FORMAT_DATE,
            'ajaxConversion' => false,
            'options' => [
                'pluginOptions' => [
                    'autoclose' => true
                ]
            ]
        ]) ?>
    </div>
</div>
