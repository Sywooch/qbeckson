<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */

if ($model->status === 0) {
    $this->title = 'Просмотр заявки';
}
if ($model->status === 3) {
    $this->title = 'Ожидается подтверждение договора';
}

$this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/organization-contracts']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-view col-md-8 col-md-offset-2">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $cert,
        'attributes' => [
            [
                'attribute' => 'number',
                'format' => 'raw',
                'value' => Html::a($cert->number, Url::to(['/certificates/view', 'id' => $cert->id]), ['class' => 'blue', 'target' => '_blank']),
            ],
            'fio_child',
        ],
    ]) ?>
    
    <?= DetailView::widget([
        'model' => $group,
        'attributes' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => Html::a($group->name, Url::to(['/groups/contracts', 'id' => $group->id]), ['class' => 'blue', 'target' => '_blank']),
            ],
        ],
    ]) ?>
    
    <?= DetailView::widget([
        'model' => $program,
        'attributes' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => Html::a($program->name, Url::to(['/programs/view', 'id' => $program->id]), ['class' => 'blue', 'target' => '_blank']),
            ],
        ],
    ]) ?>
    
    <?php
    if ($model->status === 3) {
        echo DetailView::widget([
            'model' => $program,
            'attributes' => [
                [
                    'label' => 'Посмотреть текст договора',
                    'format'=>'raw',
                    'value'=> Html::a('<span class="glyphicon glyphicon-download-alt"></span>', $model->fullUrl),
                ],
            ],
        ]);
    }
    ?>

    <?php if ($model->status === 3) : ?>
        <p class="text-justify">
            Вами выставлена оферта на заключение договора Заказчику, после получения заявления на зачисление по требуемой <a href="<?= Url::to(['application-pdf', 'id' => $model->id]) ?>"><b>форме</b></a> Вы можете зарегистрировать договор.
        </p>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->status === 3) : ?>
    <?= $form->field($model, 'applicationIsReceived')->checkbox(['onClick' => 'if ($(this).prop(\'checked\')) $("#vform").show(); else $("#vform").hide();']) ?>
    <div id="vform" style="<?= ($model->applicationIsReceived > 0 ? '' : 'display:none;')?>">
        <?= $form->field($model, 'number')->textInput(['readonly' => true]) ?>
        <?= $form->field($model, 'date')->widget(DateControl::classname(), [
            'type' => DateControl::FORMAT_DATE,
            'ajaxConversion' => false,
            'options' => [
                'pluginOptions' => [
                    'autoclose' => true
                ]
            ]
        ]) ?>
    </div>
    <?php endif; ?>

    <?= Html::a('Назад', Url::to(['/personal/organization-contracts', 'id' => $model->id]), ['class' => 'btn btn-primary']); ?>
    <?php
    if ($model->status === 0) {
        echo Html::a('Продолжить', Url::to(['/contracts/generate', 'id' => $model->id]), ['class' => 'btn btn-primary']);
    }
    if ($model->status === 3) {
        echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
    }
    ?>
    <div class="pull-right">
        <?= Html::a('Отказать', Url::to(['/contracts/no', 'id' => $model->id]), ['class' => 'btn btn-danger']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
