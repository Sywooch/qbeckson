<?php

/**
 * @var \yii\web\View $this
 */

use kartik\slider\Slider;
use yii\helpers\Html;

$js = <<<'JS'

// установить начальные значения полей "от", "до"
$('.slider-text-input').each(function() {
    var sliderId = $(this).attr('id').match('(from|to)+-(.*)'),
        sliderValue = $('input#' + sliderId[2]).val().match('^([0-9]+),([0-9]+)$');
    
    // привязать изменения slider`а к полям
    $('.field-'+sliderId[2] + ' input').on('change', function() {
        var fromToVauels = $(this).val().match('^([0-9]+),([0-9]+)$');
        $('#from-' + $(this).attr('id')).val(fromToVauels[1]);
        $('#to-' + $(this).attr('id')).val(fromToVauels[2]);
    });
    
    if (sliderId[1] == 'from') {
        $(this).val(sliderValue[1]);
    }
    
    if (sliderId[1] == 'to') {
        $(this).val(sliderValue[2]);
    }
});

// событие изменяющее значение input`а слайдера при изменении полей "от", "до"
$('.slider-text-input').on('change', function() {
    var sliderId = $(this).attr('id').match('(from|to)+-(.*)'),
        sliderInput = $('input#' + sliderId[2]),
        sliderValue = sliderInput.val().match('^([0-9]+),([0-9]+)$');

    var min = sliderInput.parent().find('.min-slider-handle').attr('aria-valuemin'),
            max = sliderInput.parent().find('.max-slider-handle').attr('aria-valuemax'),
            percentLeft = $(this).val()/(max - min)*100;   
    
    if (sliderId[1] == 'from') {
        $('input#' + sliderId[2]).val( $(this).val() + ',' + sliderValue[2]);
        
        var percentWidthFrom = (sliderValue[2]-$(this).val())/(max - min)*100;
        sliderInput.parent().find('.slider-selection').css('left', percentLeft + '%');
        sliderInput.parent().find('.slider-selection').css('width', percentWidthFrom + '%');
        
        sliderInput.parent().find('.min-slider-handle').css('left', percentLeft + '%');
    }
    
    if (sliderId[1] == 'to') {
        $('input#' + sliderId[2]).val( sliderValue[1] + ',' + $(this).val());
        
        var percentWidthTo = ($(this).val() - sliderValue[1])/(max - min)*100;
        sliderInput.parent().find('.slider-selection').css('left', sliderValue[1]/(max - min)*100 + '%');
        sliderInput.parent().find('.slider-selection').css('width', percentWidthTo + '%');
        
        sliderInput.parent().find('.max-slider-handle').css('left', percentLeft + '%');
    }
});
JS;

?>
<div class="col-md-12">
    <?php echo $form->field($model, $row['attribute'], [
        'template' => "{label} <div class=\"row\"><div class=\"col-sm-4\">{input}{error}{hint}</div></div>"
    ])->widget(Slider::classname(), [
        'pluginOptions' => [
            'min' => isset($row['pluginOptions']['min']) ? $row['pluginOptions']['min'] : 0,
            'max' => isset($row['pluginOptions']['max']) ? $row['pluginOptions']['max'] : 150000,
            'step' => 5,
            'range' => true,
        ],
        'options' => [
            'id' => $sliderId = $row['attribute'] . '-' . Yii::$app->security->generateRandomString(8)
        ]
    ])->label($model->getAttributeLabel($row['attribute'])); ?>
</div>

<?php $this->registerJs($js, $this::POS_READY); ?>

<!-- поля "от" и "до" для слайдера -->
<div class="col-md-12">
    <div class="form-group">
        <label class="control-label col-sm-3">От</label>
        <div class="col-sm-2">
            <?= Html::textInput('min', isset($row['pluginOptions']['min']) ? $row['pluginOptions']['min'] : 0, ['class' => 'slider-text-input', 'id' => 'from-' . $sliderId]) ?>
        </div>
        <label class="control-label col-sm-1">До</label>
        <div class="col-sm-2">
            <?= Html::textInput('max', isset($row['pluginOptions']['max']) ? $row['pluginOptions']['max'] : 150000, ['class' => 'slider-text-input', 'id' => 'to-' . $sliderId]) ?>
        </div>
    </div>
</div>
