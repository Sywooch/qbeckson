<?php

use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $userFilter \app\models\UserSearchFiltersAssignment */
/* @var $customizable boolean */
?>

<div class="data-search search-form well">
    <div class="row">
        <?php $form = ActiveForm::begin([
            'action' => !empty($action) ? $action : ['index'],
            'layout' => 'horizontal',
            'method' => 'get',
        ]);
        $inaccessibleRows = '';
        $otherRows = '';
        ?>
        <?php foreach ($data as $row) : ?>
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
            <?= Html::a('Сбросить', !empty($action) ? $action : ['index'],
                ['class' => 'btn btn-default', 'style' => ['color' => '#333']]) ?>&nbsp;&nbsp;
            <a href="javascript:void(0);" class="btn btn-warning show-additional-params">Расширенный поиск</a>&nbsp;&nbsp;
            <?php if ($customizable): ?>
                <a href="javascript:void(0);" class="toggle-search-settings">
                    <span class="glyphicon glyphicon-cog"></span> настроить
                </a>
            <?php endif; ?>
        </div>
        <?php ActiveForm::end(); ?>
        <?php if ($customizable): ?>
            <div class="col-md-12 search-settings collapse">
                <br/>
                <?php
                $idHiden = 'usersearchfiltersassignment-filter_id-'
                    . Yii::$app->security->generateRandomString(6);
                $idSelect = Yii::$app->security->generateRandomString(6);
                $form = ActiveForm::begin(['action' => ['site/save-filter']]);
                $columnLabels = [];
                foreach (array_unique($userFilter->filter->columnsForUser) as $column) {
                    $columnLabels[$column] = $model->getAttributeLabel($column);
                }
                ?>
                <?= $form->field(
                    $userFilter,
                    'filter_id'
                )->hiddenInput(['id' => $idHiden])->label(false) ?>
                <?= $form->field($userFilter, 'columns')->widget(Select2::class, [
                    'data' => array_combine(array_unique($userFilter->filter->columnsForUser), $columnLabels),
                    'options' => [
                        'placeholder' => 'Выберите..',
                        'id' => $idSelect
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true,
                    ],
                ]); ?>
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-warning']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
