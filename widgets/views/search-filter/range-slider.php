<?php

use yii\helpers\Html;
use kartik\slider\Slider;

?>
<div class="col-md-12">
    <?php echo $form->field($model, $row['attribute'])->widget(Slider::classname(), [
        'sliderColor' => Slider::TYPE_GREY,
        'handleColor' => Slider::TYPE_DANGER,
        'pluginOptions' => [
            'handle' => 'triangle',
            'min' => 0,
            'max' => 150000,
            'step' => 10,
            'range' => true
        ]
    ]); ?>
</div>
