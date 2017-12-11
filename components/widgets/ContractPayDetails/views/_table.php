<?php

\yii\widgets\Pjax::begin([
    'timeout' => 10000,
    'enablePushState' => false
]);
echo \yii\grid\GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id',
                'label' => '№ транзакции',
            ],
            [
                'attribute' => 'date',
                'label' => 'Дата',
                'format' => ['date', 'php:M Y']
            ],
            'paidType',
            //'preinvoiceLabel',
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
