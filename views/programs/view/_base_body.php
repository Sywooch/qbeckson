<?php

/** @var $model \app\models\Programs */
/** @var $this yii\web\View */

echo \yii\bootstrap\Tabs::widget([
    'items'       => array_map(function ($module)
    {
        /** @var $module \app\models\ProgrammeModule */
        /** @var $this yii\web\View */
        $result = [];
        $result['label'] = $module->getShortName();
        $result['content'] = $this->render('_base_module', ['model' => $module]);

        return $result;
    }, $model->modules),
    'itemOptions' => ['class' => 'program-info-view'],
]);


?>

