<?php

use app\models\CertGroup;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

?>
<div class="col-md-12">
    <?= $form->field($model, $row['attribute'])->widget(Select2::class, [
        'data' => $row['data'],
        'options' => [
            'multiple' => true,
            'id' => $row['attribute'] . '-' . Yii::$app->security->generateRandomString(8)
        ],
    ])->label($model->getAttributeLabel($row['attribute'])); ?>
</div>
