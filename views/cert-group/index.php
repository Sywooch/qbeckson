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
                'label' => '<span title="Текущий период: с ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_from) . ' до ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->current_program_date_to) . '">Номинал</span>',
                'encodeLabel' => false,
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
                'label' => '<span title="Будущий период: с ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_from) . ' до ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_to) . '">Номинал будущего периода</span>',
                'encodeLabel' => false,
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
            'countCertificates',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'amount',
                'pageSummary' => false,
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
        ],
    ]); ?>
</div>
