<?php

use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \app\models\forms\ContractRequestForm */
/* @var $form ActiveForm */

$this->title = 'Записаться';
$this->params['breadcrumbs'][] = ['label' => 'Поиск программ', 'url' => ['programs/search']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $pjax = Pjax::begin() ?>
<div class="contract-request">
    <?php $form = ActiveForm::begin([
        'options' => [
            'data-pjax' => '',
        ]
    ]); ?>
    <div class="row">
        <div class="col-md-offset-1 col-md-10">
            <p class="lead">Realization period: <?= $model->dateFrom; ?> <?= $model->dateTo; ?></p>
            <?= $form->field($model, 'dateFrom')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd'
                ]
            ]) ?>
        </div>
        <div class="col-md-offset-1 col-md-11">
            <div class="form-group">
                <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <?php $form::end(); ?>
</div>
<?php $pjax::end() ?>
