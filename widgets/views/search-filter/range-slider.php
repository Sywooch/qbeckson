<?php

use kartik\slider\Slider;

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
            'id' => $row['attribute'] . '-' . Yii::$app->security->generateRandomString(8)
        ]
    ])->label($model->getAttributeLabel($row['attribute'])); ?>
</div>
