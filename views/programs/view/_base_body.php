<?php

/** @var $model \app\models\Programs */
/** @var $this yii\web\View */

/** @var $cooperate Cooperate */

use app\models\Cooperate;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

echo \yii\bootstrap\Tabs::widget([
    'items' => array_map(function ($module) use ($cooperate)
    {
        /** @var $module \app\models\module\ModuleViewDecorator */
        /** @var $this yii\web\View */
        $result = [];
        $result['label'] = $module->getShortName();
        $result['content'] = $this->render(
            $module->getModuleTemplate(),
            ['model' => $module, 'cooperate' => $cooperate]
        );

        return $result;
    }, $modules),
    'itemOptions' => ['class' => 'program-info-view'],
    'navType'     => 'new-nav-tabs'
]); ?>

<?php if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)): ?>
    <?php $form = ActiveForm::begin([
        'id' => 'enable-auto-prolongation-form',
        'action' => Url::to(['/programs/change-auto-prolongation', 'id' => $model->id]),
        'enableAjaxValidation' => true,
    ]) ?>

    <?= $form->field($model, 'auto_prolongation_enabled')->checkbox() ?>

    <?php $form->end() ?>
<?php endif; ?>
