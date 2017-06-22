<?php

use yii\helpers\Html;

?>
<div class="col-md-3">
    <?php echo $form->field($model, $row['attribute'])->dropDownList($row['data'], ['prompt' => 'Не важно']) ?>
</div>
