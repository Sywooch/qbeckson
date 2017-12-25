<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\Programs
 */

$programmeTable = $this->render('_history_programme_table', ['model' => $model]);

$modules = $model->modules;

$modulesItems = array_map(
    function (\app\models\ProgrammeModule $module) {
        /**@var $this \yii\web\View */

        return
            [
                'label' => 'История модуля: ' . $module->name,
                'content' => $this->render('_history_module_table', ['module' => $module]),
                'contentOptions' => [],
                'options' => [],
                //'footer' => 'Footer'
            ];
    },
    $modules
);
$programmeItem = [
    [
        'label' => 'История программы',
        'content' => [
            $programmeTable,
            \yii\bootstrap\Collapse::widget([
                'items' => $modulesItems
            ]),
        ],
        'contentOptions' => [],
        'options' => [],
        //'footer' => 'Footer'
    ],
];

echo \yii\bootstrap\Collapse::widget([
    'items' => $programmeItem
]);
