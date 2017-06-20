<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="data-search search-form well">

    <div class="row">
        <?php $form = ActiveForm::begin([
            'action' => !empty($action) ? $action : ['index'],
            'method' => 'get',
        ]);
        $inaccessibleRows = '';
        $otherRows = '';
        ?>
        <?php foreach ($data as $row): ?>
            <?php
            $result = $this->render($row['type'], [
                'form' => $form,
                'model' => $model,
                'row' => $row,
            ]);
            if (in_array($row['attribute'], $userFilter->filter->inaccessibleColumns)) {
                $inaccessibleRows .= $result;
            } else {
                $otherRows .= $result;
            }
            ?>
        <?php endforeach; ?>

        <?= $inaccessibleRows ?>
        <div class="col-md-12 additional-params collapse">
            <div class="row">
                <?= $otherRows ?>
            </div>
        </div>

        <div class="col-md-12">
            <?= Html::submitButton('Начать поиск', ['class' => 'btn btn-primary']) ?>&nbsp;&nbsp;
            <?= Html::a('Сбросить', !empty($action) ? $action : ['index'], ['class' => 'btn btn-default']) ?>&nbsp;&nbsp;
            <a href="javascript:void(0);" class="btn btn-warning show-additional-params">Расширенный поиск</a>&nbsp;&nbsp;
            <a href="javascript:void(0);" class="toggle-search-settings">
                <span class="glyphicon glyphicon-cog"></span> настроить
            </a>
        </div>
        <?php ActiveForm::end(); ?>
        <div class="col-md-12 search-settings collapse">
            <br/>
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
        </div>
    </div>
</div>
