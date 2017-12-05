<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\Programs
 */

use app\components\periodicField\PeriodicFieldAR;

$programmeTable = \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getHistoryQuery(),
        'sort' => [
            'defaultOrder' => [
                'field_name' => ['field_name' => SORT_ASC, 'created_at' => SORT_ASC]
            ]
        ]
    ]),
    'columns' => PeriodicFieldAR::getColumns(),
]);

$modules = $model->modules;

$modulesItems = array_map(
    function (\app\models\ProgrammeModule $module) {
        $dataProvider = new \yii\data\ActiveDataProvider(
            [
                'query' => $module->getHistoryQuery(),
                'sort' => [
                    'defaultOrder' => [
                        'field_name' => ['field_name' => SORT_ASC, 'created_at' => SORT_ASC]
                    ]
                ]
            ]
        );
        $grid = \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => PeriodicFieldAR::getColumns(),
        ]);

        return
            [
                'label' => 'История модуля: ' . $module->name,
                'content' => $grid,
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
            $programmeTable
        ],
        'contentOptions' => [],
        'options' => [],
        //'footer' => 'Footer'
    ],
];

$items = array_merge($programmeItem, $modulesItems);

echo \yii\bootstrap\Collapse::widget([
    'items' => $items
]);
