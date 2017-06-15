<?php

use yii\helpers\Html;
use kartik\widgets\TouchSpin;

?>
<div class="col-md-3">
    <?php echo $form->field($model, $row['attribute'])->widget(TouchSpin::classname(), [
        'options' => ['placeholder' => $model->getAttributeLabel($row['attribute'])],
    ]); ?>
</div>
