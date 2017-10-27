<?php

use yii\helpers\Html;

?>
<div class="col-md-12">
    <?php echo $form->field($model, $row['attribute'], [
        'horizontalCssClasses' => [
            'wrapper' => 'col-sm-2',
        ]
    ])->dropDownList($row['data'], ['multiple' => true]) ?>
</div>
