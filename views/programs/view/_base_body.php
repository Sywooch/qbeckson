<?php

/**
 * @var $model ProgramViewDecorator
 * @var $this View
 * @var $group Groups
 */

use app\models\Groups;
use app\models\Programs\ProgramViewDecorator;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

echo \yii\bootstrap\Tabs::widget([
    'items' => array_map(function ($module) use ($model)
    {
        /** @var $module \app\models\module\ModuleViewDecorator */
        /** @var $this yii\web\View */
        $result = [];
        $result['label'] = $module->getShortName();
        $result['content'] = $this->render(
            $model->getModuleTemplate(),
            ['model' => $module]
        );

        return $result;
    }, $modules),
    'itemOptions' => ['class' => 'program-info-view'],
    'navType'     => 'new-nav-tabs'
]); ?>

<?php if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)): ?>

    <?php

    $js = <<<js
        $('.new-group-auto-prolongation-button').on('click', function() {
            var url = $(this).data('url'),
                groupId = $(this).data('group-id'),
                modal = $('#' + $(this).data('modal'));
            
            $.ajax({
                url: url,
                method: 'POST',
                data: {groupId: groupId},
                success: function(data) {
                    $('.auto-prolongation-to-new-group-block').html(data);
                    
                    modal.modal();
                }
            });
        });
js;
    $this->registerJs($js);

    ?>

    <?php Modal::begin([
        'id' => 'new-group-auto-prolongation-modal',
        'size' => Modal::SIZE_LARGE,
        'header' => 'Перевод детей в другой модуль',
    ]) ?>
    <div class="auto-prolongation-to-new-group-block"></div>
    <?php Modal::end() ?>
    <?php Modal::begin([
        'id' => 'auto-prolong-confirmation-modal',
        'clientOptions' => ['backdrop' => false]
    ]); ?>
    <div id="auto-prolong-confirmation-block"></div>
    <?php Modal::end() ?>
    <?php Modal::begin([
        'id' => 'group-create-modal',
        'clientOptions' => ['backdrop' => false]
    ]) ?>
    <div class="group-create-message alert-danger alert fade in" style="display: none;"></div>
    <div class="group-create-block"></div>
    <div class="form-group">
        <?= Html::button('Сохранить', ['class' => 'btn btn-success group-save-button']) ?>
        <?= Html::button('Отмена', ['class' => 'btn btn-danger', 'onClick' => '$(this).parents(".modal").first().modal("hide");']) ?>
    </div>

    <?php Modal::end() ?>

    <?php $form = ActiveForm::begin([
        'id' => 'enable-auto-prolongation-form',
        'action' => Url::to(['/programs/change-auto-prolongation', 'id' => $model->id]),
        'enableAjaxValidation' => true,
    ]) ?>

    <?= $form->field($model, 'auto_prolongation_enabled')->checkbox() ?>

    <?php $form->end() ?>
<?php endif; ?>
