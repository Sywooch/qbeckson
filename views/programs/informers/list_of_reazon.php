<?php

/**
 * @var $dataProvider yii\data\ActiveDataProvider
 */

echo \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'summary' => 'Причины отказов, всего: {totalCount}',
    'options' => ['tag' => 'ul', 'class' => 'list-unstyled'],
    'itemOptions' => ['tag' => 'li'],
    'itemView' => function ($model, $key, $index, $widget)
    {
        /**@var $model \app\models\Informs */
        return $model->date . ': ' . nl2br($model->text);
    }
]);