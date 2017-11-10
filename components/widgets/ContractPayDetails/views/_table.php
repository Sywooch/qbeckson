<?php
\yii\widgets\Pjax::begin([
    'timeout' => 10000,
    'enablePushState' => false
]);
echo \yii\grid\GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'columns' => [
            ['label' => '№ транзакции',
                'value' => function ($model) {
                    /**@var $model \app\models\Completeness */
                    return $model->year . $model->month . $model->id;
                }
            ],
            [
                'attribute' => 'sum',
                'format' => 'currency',
                'value' => function ($model) {
                    /**@var $model \app\models\Completeness */
                    return $model->sum;
                }

            ],

        ]
    ]
);
\yii\widgets\Pjax::end();
