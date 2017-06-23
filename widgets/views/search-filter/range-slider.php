<?php

use yii\helpers\Html;
use kartik\slider\Slider;

?>
<div class="col-md-12">
    <?php echo $form->field($model, $row['attribute'])->widget(Slider::classname(), [
        'pluginOptions' => [
            'min' => -1,
            'max' => 150000,
            'step' => 10,
            'range' => true
        ]
    ])->label($model->getAttributeLabel($row['attribute'])); ?>
</div>
