<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 07.12.17
 * Time: 16:35
 * @var $this \yii\web\View
 * @var $model \app\models\Programs
 *
 */

use app\components\periodicField\PeriodicFieldAR;

\yii\widgets\Pjax::begin(['enablePushState' => false, 'timeout' => 3000]);
echo \yii\grid\GridView::widget([
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
\yii\widgets\Pjax::end();
