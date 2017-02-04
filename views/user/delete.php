<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */

if (isset($title)) {
    $this->title = $title;
}
else {
    $this->title = 'Удалить плательщика';
}
?>
<div class="user-delete">

    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= Html::encode($this->title) ?></h4>
          </div>
          <div class="modal-body">
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($user, 'confirm')->passwordInput() ?>

                <?= Html::submitButton('Подтвердить', ['class' => 'btn btn-danger']) ?>

                <?php ActiveForm::end(); ?>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

</div>
