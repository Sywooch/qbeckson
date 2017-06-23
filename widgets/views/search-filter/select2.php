<?php

use app\models\CertGroup;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

?>
<div class="col-md-12">
    <?= $form->field($model, $row['attribute'])->widget(Select2::class, [
        'data' => ArrayHelper::map(
            CertGroup::findAll(['payer_id' => Yii::$app->user->getIdentity()->payer->id]),
            'id',
            'group'
        ),
        'options' => ['multiple' => true],
    ])->label($model->getAttributeLabel($row['attribute'])); ?>
</div>
