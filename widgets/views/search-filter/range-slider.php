<?php

use kartik\slider\Slider;

?>
<div class="col-md-12">
    <?php echo $form->field($model, $row['attribute'], [
        'template' => "{label} <div class=\"row\"><div class=\"col-sm-4\">{input}{error}{hint}</div></div>"
    ])->widget(Slider::classname(), [
        'pluginOptions' => [
            'min' => -1,
            'max' => 150000,
            'step' => 10,
            'range' => true,
        ]
    ])->label($model->getAttributeLabel($row['attribute'])); ?>
</div>
