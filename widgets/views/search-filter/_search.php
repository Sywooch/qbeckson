<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="data-search search-form" style="display:none;">

    <?php $form = ActiveForm::begin([
        'action' => !empty($action) ? $action : ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <?php foreach ($data as $row): ?>
            <?php echo $this->render($row['type'], [
                'form' => $form,
                'model' => $model,
                'row' => $row,
            ]) ?>
        <?php endforeach; ?>

        <div class="col-md-12">
            <?= Html::submitButton('Начать поиск', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <br/>

    <?php ActiveForm::end(); ?>

</div>
