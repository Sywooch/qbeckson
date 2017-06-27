<?php

use yii\helpers\Html;
use kartik\widgets\TouchSpin;

?>
<div class="col-md-12">
    <?php echo $form->field($model, $row['attribute'], [
        'horizontalCssClasses' => [
            'wrapper' => 'col-sm-2',
        ]
    ])->widget(TouchSpin::classname(), [
        'options' => ['placeholder' => $model->getAttributeLabel($row['attribute'])],
    ]); ?>
</div>
