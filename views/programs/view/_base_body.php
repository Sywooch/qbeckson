<?php

/** @var $model \app\models\Programs */
/** @var $this yii\web\View */

/** @var $cooperate Cooperate */

use app\models\Cooperate;

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
]);


?>

