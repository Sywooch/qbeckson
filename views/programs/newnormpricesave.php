<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */

if (isset($title)) {
    $this->title = $title;
}
else {
    $this->title = 'Изменить нормативную стоимость';
}
?>
<div class="user-delete">

    <div class="modal fade modal-auto-popup">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= Html::encode($this->title) ?></h4>
          </div>
          <div class="modal-body">
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'normative_price')->textInput() ?>

                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>

                <?php ActiveForm::end(); ?>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

</div>
