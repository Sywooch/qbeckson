<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = 'Редактировать программу: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/organization-programs']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="programs-update col-md-10 col-md-offset-1">
    
    <div class="modal fade modal-auto-popup">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Внимание!</h4>
          </div>
          <div class="modal-body">
            После редактирования программы, она отправляется на повторную сертификацию.
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <h1><?= Html::encode($this->title) ?></h1>

     <?= $this->render('_form', [
        'model' => $model,
        'file' => $file,
        'modelsYears' => $modelYears
    ]) ?>

</div>
