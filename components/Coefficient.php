<?php

namespace app\components;

use yii\base\Component;
use yii\base\Event;
use yii\web\BadRequestHttpException;
use app\models\Coefficient as CoefficientModel;

class Coefficient extends Component
{
    public function getData()
    {
        if ($model = CoefficientModel::findOne(['operator_id' => GLOBAL_OPERATOR])) {
            return $model;
        }

        throw new BadRequestHttpException('Коэффициенты не найдены.');
    }
}
