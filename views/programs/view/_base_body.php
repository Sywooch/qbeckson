<?php

/** @var $model \app\models\Programs */
/** @var $this yii\web\View */

/** @var $cooperate Cooperate */

use app\models\Cooperate;

$moduleTemplate = '_base_module';
if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_CERTIFICATE)) {
    $moduleTemplate = '_certificate_module';
} elseif (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)) {
    $moduleTemplate = '_organisation_module';
} elseif (Yii::$app->user->can(\app\models\UserIdentity::ROLE_OPERATOR)) {
    $moduleTemplate = '_operator_module';
}


echo \yii\bootstrap\Tabs::widget([
    'items'       => array_map(function ($module) use ($moduleTemplate, $cooperate)
    {
        /** @var $module \app\models\ProgrammeModule */
        /** @var $this yii\web\View */
        $result = [];
        $result['label'] = $module->getShortName();
        $result['content'] = $this->render($moduleTemplate, ['model' => $module, 'cooperate' => $cooperate]);

        return $result;
    }, $modules),
    'itemOptions' => ['class' => 'program-info-view'],
    'navType'     => 'new-nav-tabs'
]);


?>

