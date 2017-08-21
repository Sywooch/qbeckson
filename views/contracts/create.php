<?php

use app\models\Programs;
use kartik\widgets\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Organization;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $validateForm \app\models\forms\CertificateVerificationForm */

$this->title = 'Создать договор';
$this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['personal/organization-contracts']];
$this->params['breadcrumbs'][] = $this->title;

/** @var Organization $organization */
$organization = Yii::$app->user->getIdentity()->organization;
?>
<?php Pjax::begin(); ?>
<div class="programs-view col-md-6 col-md-offset-3">
   <h1><?= Html::encode($this->title) ?></h1>
    <?php if ($organization->max_child > $organization->getContracts()->andWhere(['status' => [0, 1, 3]])->count()) : ?>
        <?php $form = ActiveForm::begin([
            'options' => [
                'data-pjax' => true
            ]
        ]); ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <?= $form->field($validateForm, 'number')->textInput(['maxlength' => true]) ?>
                <?= $form->field($validateForm, 'soname')->textInput(['maxlength' => true]) ?>
                <?= $form->field($validateForm, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($validateForm, 'patronymic')->textInput(['maxlength' => true]) ?>
                <?php if (null === $selectForm) : ?>
                    <div class="form-group">
                        <?= Html::submitButton('Проверить', ['class' => 'btn btn-primary btn-block']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if (null !== $selectForm) : ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <p class="lead">Выберите группу</p>
                    <?= $form->field($selectForm, 'programId')
                        ->dropDownList(
                            ArrayHelper::map(Programs::findAll([
                                'organization_id' => Yii::$app->user->identity->organization->id
                            ]), 'id', 'name'),
                            ['id' => 'program-id', 'prompt' => '-- Не выбрана --']
                        ) ?>
                    <?= $form->field($selectForm, 'moduleId')->widget(DepDrop::class, [
                        'options' => ['id' => 'module-id'],
                        'pluginOptions' => [
                            'depends' => ['program-id'],
                            'placeholder' => '-- Не выбран --',
                            'url' => Url::to(['contracts/select-module'])
                        ]
                    ]); ?>
                    <?= $form->field($selectForm, 'groupId')->widget(DepDrop::class, [
                        'options' => ['id' => 'group-id'],
                        'pluginOptions' => [
                            'depends' => ['program-id', 'module-id'],
                            'placeholder' => '-- Не выбрана --',
                            'url' => Url::to(['contracts/select-group'])
                        ]
                    ]); ?>
                    <div class="form-group">
                        <?= Html::submitButton('Перейти к формированию заявки', ['class' => 'btn btn-success btn-block']) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php ActiveForm::end(); ?>
    <?php else : ?>
        <div class="well">
            <p>Записано максимальное количество детей.</p>
        </div>
    <?php endif; ?>
</div>
<?php Pjax::end(); ?>
