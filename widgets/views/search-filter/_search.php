<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

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
            <?= Html::submitButton('Начать поиск', ['class' => 'btn btn-primary']) ?>&nbsp;&nbsp;&nbsp;<a
                    href="javascript:void(0);" class="toggle-search-settings"><span
                        class="glyphicon glyphicon-cog"></span> настроить</a>
        </div>
        <?php ActiveForm::end(); ?>
        <div class="col-md-12 search-settings hidden">
            <br /><br />
            <?php
            $form = ActiveForm::begin(['action' => ['site/save-filter']]);
            $columnLabels = [];
            foreach ($userFilter->filter->columnsForUser as $column) {
                $columnLabels[$column] = $model->getAttributeLabel($column);
            }
            ?>
            <?= $form->field($userFilter, 'filter_id')->hiddenInput()->label(false) ?>
            <?= $form->field($userFilter, 'columns')->widget(Select2::classname(), [
                'data' => array_combine($userFilter->filter->columnsForUser, $columnLabels),
                'options' => ['placeholder' => 'Выберите..'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]); ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-warning']) ?>
            <?php ActiveForm::end(); ?>
            <br /><br />
        </div>
    </div>
    <br/>

</div>
