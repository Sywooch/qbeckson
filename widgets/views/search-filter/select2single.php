<?php

use kartik\select2\Select2;

?>
<div class="col-md-12">
    <?= $form->field($model, $row['attribute'])->widget(Select2::class, [
        'data'    => $row['data'],
        'options' => [
            'multiple' => false,
            'id'       => $row['attribute'] . '-' . Yii::$app->security->generateRandomString(8)
        ],
    ])->label($model->getAttributeLabel($row['attribute'])); ?>
</div>
