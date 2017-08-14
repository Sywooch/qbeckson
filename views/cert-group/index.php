<?php

use kartik\grid\EditableColumn;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CertGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Номиналы групп';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cert-group-index col-md-10 col-md-offset-1">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'columns' => [
            [
                'class' => EditableColumn::class,
                'attribute' => 'group',
                'pageSummary' => true,
                'editableOptions' => [
                    'asPopover' => false,
                    'submitButton' => [
                        'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                        'class' => 'btn btn-sm btn-success',
                    ],
                ],
                'readonly' => function ($model, $key, $index, $widget) {
                    if ($model->is_special) {
                        return true;
                    }

                    return false;
                },
            ],
            [
                'class' => EditableColumn::class,
                'attribute' => 'nominal',
                'pageSummary' => true,
                'editableOptions' => [
                    'submitButton' => [
                        'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                        'class' => 'btn btn-sm btn-success',
                    ],
                    'afterInput' => function ($form, $widget) {
                        echo '<br>' .
                            Html::passwordInput(
                                'password',
                                '',
                                ['class' => 'form-control', 'placeholder' => 'Введите пароль']
                            );
                    }
                ],
                'readonly' => function ($model, $key, $index, $widget) {
                    if ($model->is_special) {
                        return true;
                    }

                    return false;
                },
            ],
            [
                'class' => EditableColumn::class,
                'attribute' => 'nominal_f',
                'pageSummary' => true,
                'editableOptions' => [
                    'submitButton' => [
                        'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                        'class' => 'btn btn-sm btn-success',
                    ],
                    'afterInput' => function ($form, $widget) {
                        echo '<br>' .
                            Html::passwordInput(
                                'password',
                                '',
                                ['class' => 'form-control', 'placeholder' => 'Введите пароль']
                            );
                    }
                ],
                'readonly' => function ($model, $key, $index, $widget) {
                    if ($model->is_special) {
                        return true;
                    }

                    return false;
                },
            ],
        ],
    ]); ?>
</div>
