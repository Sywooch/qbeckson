<?php

use app\models\forms\SelectGroupForm;
use app\models\Programs;
use kartik\widgets\DepDrop;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Organization;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $validateForm \app\models\forms\CertificateVerificationForm */
/* @var $selectForm SelectGroupForm */
/* @var $programIdNameList array */

$this->title = 'Создать договор';
$this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['personal/organization-contracts']];
$this->params['breadcrumbs'][] = $this->title;

/** @var Organization $organization */
$organization = Yii::$app->user->getIdentity()->organization;

$js = <<<JS
var program = $('body').find('#program-id'),
    module = $('body').find('#module-id');

module.on('click', function() {
    $.ajax({
        url: '/contracts/contract-can-auto-prolong-in-module',
        method: 'POST',
        data: {certificateId: $('.contract-can-auto-prolong').data('certificate-id'), programId: program.val(), moduleId: module.val()},
        success: function(data) {
            console.log(data);
        }
    });
});
JS;
$this->registerJs($js);

?>
<?php Pjax::begin(); ?>
<div class="programs-view col-md-6 col-md-offset-3">
   <h1><?= Html::encode($this->title) ?></h1>
    <?php if ($organization->max_child > $organization->getContracts()->andWhere(['status' => [0, 1, 3]])->count()) : ?>
        <?php $form = ActiveForm::begin([
            'id' => 'contract-request-form',
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
                        <?= Html::submitButton('Проверить', ['class' => 'btn btn-primary btn-block qwe']) ?>
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
                            $programIdNameList,
                            ['id' => 'program-id', 'prompt' => '-- Не выбрана --']
                        ) ?>
                    <?= $form->field($selectForm, 'moduleId')->widget(DepDrop::class, [
                        'options' => ['id' => 'module-id',
                            'onClick' => '$.ajax({
                                    url: \'/contracts/contract-can-auto-prolong-in-module\',
                                    method: \'POST\',
                                    data: {certificateId: $(\'.contract-can-auto-prolong\').data(\'certificate-id\'), programId: $(\'#program-id\').val(), moduleId: $(\'#module-id\').val()},
                                    success: function(data) {
                                        if (data == true) {
                                            $(\'.contract-can-auto-prolong\').show();
                                            $(\'.contract-cant-auto-prolong\').hide();
                                        } else {
                                            $(\'.contract-can-auto-prolong\').hide();
                                            $(\'.contract-cant-auto-prolong\').show();
                                        }
                                    }
                                });'
                            ],
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
                        <div class="contract-can-auto-prolong" data-certificate-id="<?= $selectForm->getCertificate()->id ?>" style="display: none;">
                            <?= Html::button('Перейти к формированию заявки', ['class' => 'btn btn-success btn-block', 'onClick' => '$("#auto-prolongation-modal").modal();']) ?>
                        </div>
                        <div class="contract-cant-auto-prolong">
                            <?= Html::submitButton('Перейти к формированию заявки', ['class' => 'btn btn-success btn-block']) ?>
                        </div>
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

<?php Modal::begin([
    'id' => 'auto-prolongation-modal',
    'header' => 'Подача новой заявки',
]) ?>

<p>Выбранная образовательная услуга предполагает продолжение освоение программы,
    по которой уже осуществлялось обучение ребенка. Вы уверены, что хотите подать новую отдельную заявку,
    а не дождаться пролонгации договора?</p>

<?= Html::button('Да, подать новую заявку', ['class' => 'btn btn-success', 'onClick' => '$("#contract-request-form").submit();']) ?>
<?= Html::button('Нет, дождаться пролонгации договора', ['class' => 'btn btn-primary margin', 'onClick' => '$("#wait-for-contract-auto-prolongation-modal").modal();']) ?>

<?php Modal::end() ?>

<?php Modal::begin([
    'id' => 'wait-for-contract-auto-prolongation-modal',
    'header' => 'Дождаться пролонгации договора',
    'clientOptions' => ['backdrop' => false]
]) ?>
<p>!!!Поставщик услуг по завершению срока действия договора самостоятельно сформирует оферту.
    Если Вы в течение установленного срока не отзовете ее – договор на продолжение обучения вступит в силу</p>
<?= Html::button('Закрыть', ['class' => 'btn btn-primary', 'onClick' => '$(".modal").modal("hide")']) ?>
<?php Modal::end() ?>
