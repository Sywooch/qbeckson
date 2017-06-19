<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="data-search search-form">

    <div class="row">
        <?php $form = ActiveForm::begin([
            'action' => !empty($action) ? $action : ['index'],
            'method' => 'get',
        ]); ?>
        <?php foreach ($data as $row): ?>
            <?php echo $this->render($row['type'], [
                'form' => $form,
                'model' => $model,
                'row' => $row,
            ]) ?>
        <?php endforeach; ?>

        <div class="col-md-12">
            <?= Html::submitButton('Начать поиск', ['class' => 'btn btn-primary']) ?>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="toggle-search-settings"><span class="glyphicon glyphicon-cog"></span> настроить</a>
        </div>
        <?php ActiveForm::end(); ?>
        <div class="col-md-12 search-settings hidden">
        </div>
    </div>
    <br/>

</div>
