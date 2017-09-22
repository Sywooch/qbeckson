<?php

use app\components\widgets\modalCheckLink\ModalCheckLink;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

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
if ($model->status === \app\models\Contracts::STATUS_REFUSED) {
    echo '<div class="alert alert-warning">' . array_pop($model->informs)->text . '</div>';
}
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
        'model' => $model,
        'attributes' => [
            'start_edu_contract:date',
            'stop_edu_contract:date',
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
            [
                'label' => 'Модуль',
                'value' => $model->year->fullname,
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
        <?= Html::submitButton('Зарегистрировать договор', ['class' => 'btn btn-success']); ?><br /><br />
    </div>
    <?php endif; ?>

    <?= Html::a('Назад', Url::to(['/personal/organization-contracts', 'id' => $model->id]), ['class' => 'btn btn-primary']); ?>
    <?php
    if ($model->status === 0) {
        echo Html::a('Продолжить', Url::to(['/contracts/generate', 'id' => $model->id]), ['class' => 'btn btn-primary']);
    }
    ?>
    <div class="pull-right">
        <?= $model->status !== \app\models\Contracts::STATUS_REFUSED ? ModalCheckLink::widget([
            'link'          => Html::a('Отказать', Url::to(['/contracts/no', 'id' => $model->id]), ['class' => 'btn btn-danger']),
            'buttonOptions' => ['label' => 'Отказать', 'class' => 'btn btn-danger'],
            'content'       => 'Отклоняя заявку Вы отказываетесь от заключения договора на обучение по выбранной программе. Деньги, зарезервированные на оплату договора вернутся на сертификат в полном объеме, но передумывать отклонять заявку будет уже поздно. Вы уверены, что хотите отклонить заявку?',
            'label'         => 'Да, я уверен, что хочу отклонить заявку',
            'title'         => 'Отклонить заявку на обучение?'
        ]) : \yii\bootstrap\Button::widget(['label'   => 'Уже отказано',
                                            'options' => ['class'    => 'btn btn-danger',
                                                          'disabled' => 'disabled'],]) ?>

    </div>
    <?php ActiveForm::end(); ?>
</div>
