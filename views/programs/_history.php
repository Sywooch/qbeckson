<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\Programs
 */

$programmeTable = \yii\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getHistoryQuery(),
        'sort' => [
            'defaultOrder' => [
                'field_name' => ['field_name' => SORT_ASC, 'created_at' => SORT_ASC]
            ]
        ]
    ]),
    'columns' => [
        'fieldLabel',
        'created_at:datetime',
        'created_by:userName',
        'resolvedValue'
    ],
]);

echo \yii\bootstrap\Collapse::widget([
    'items' => [
        [
            'label' => 'История программы',
            'content' => [
                $programmeTable
            ],
            'contentOptions' => [],
            'options' => [],
            'footer' => 'Footer'
        ],
    ]
]);
