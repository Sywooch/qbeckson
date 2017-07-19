<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\Event;
use yii\web\BadRequestHttpException;
use app\models\Coefficient as CoefficientModel;

class Coefficient extends Component
{
    public function getData()
    {
        if ($model = CoefficientModel::findOne(['operator_id' => Yii::$app->operator->identity->id])) {
            return $model;
        }

        throw new BadRequestHttpException('Коэффициенты не найдены.');
    }
}
