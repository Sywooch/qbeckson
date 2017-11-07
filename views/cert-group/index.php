<?php

use app\models\Payers;
use kartik\grid\EditableColumn;
use kartik\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CertGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $payer Payers */

$this->title = 'Номиналы групп';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs("jQuery('#payers-certificate_can_use_future_balance').click(function(){jQuery.post('/payers/save-params', {'Payers[certificate_can_use_future_balance]': jQuery(this).prop('checked') ? 1 : 0}).done(function(data){});});");

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
            'countActualCertificates',
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

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($payer, 'days_to_first_contract_request')->textInput() ?>
    <?= $form->field($payer, 'days_to_contract_request_after_refused')->textInput() ?>
    <?= $form->field(Yii::$app->user->identity->payer, 'certificate_can_use_future_balance')->checkbox() ?>

    <?= Html::submitButton('сохранить', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>

</div>
