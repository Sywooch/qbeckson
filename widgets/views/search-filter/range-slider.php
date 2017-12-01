<?php

/**
 * @var \yii\web\View $this
 */

use kartik\field\FieldRange;
use kartik\slider\Slider;

\app\assets\sliderWithRange\SliderWithRangeAsset::register($this);
?>
<div class="col-md-12">
    <?php

    $sliderId = $row['attribute'] . '-' . Yii::$app->security->generateRandomString(8);
    $inputId1 = $sliderId . '-part1';
    $inputId2 = $sliderId . '-part2';
    $input = FieldRange::widget([
        'name1' => $row['attribute'] . '-part1',
        'name2' => $row['attribute'] . '-part2',
        'useAddons' => false,
        'template' => '{widget}',
        'options1' => ['id' => $inputId1],
        'options2' => ['id' => $inputId2],

    ]);

    echo $form->field($model, $row['attribute'], [
        'template' => "{label} <div class=\"row\"><div class=\"col-sm-4\">{input}{error}$input{hint}</div></div>"
    ])->widget(Slider::classname(), [
        'pluginOptions' => [
            'min' => isset($row['pluginOptions']['min']) ? $row['pluginOptions']['min'] : 0,
            'max' => isset($row['pluginOptions']['max']) ? $row['pluginOptions']['max'] : 150000,
            'step' => 5,
            'range' => true,

        ],
        'options' => [
            'id' => $sliderId
        ]
    ])->label($model->getAttributeLabel($row['attribute'])); ?>
</div>

<?php $jsSlider = <<<JS
    $('#$sliderId').SliderWithRange({fieldFrom: $('#$inputId1'), fieldTo: $('#$inputId2')});
JS;
$this->registerJs($jsSlider);
