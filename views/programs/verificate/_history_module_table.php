<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 07.12.17
 * Time: 16:37
 * @var $module \app\models\ProgrammeModule
 */

use app\components\periodicField\PeriodicFieldAR;

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
\yii\widgets\Pjax::begin(['enablePushState' => false, 'timeout' => 3000]);
echo \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => PeriodicFieldAR::getColumns(),
]);
\yii\widgets\Pjax::end();
