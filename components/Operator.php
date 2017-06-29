<?php

namespace app\components;

use yii\base\Component;
use yii\base\Event;
use yii\web\BadRequestHttpException;
use app\models\Operators;

class Operator extends Component
{
    public function getIdentity()
    {
        if ($model = Operators::findOne(GLOBAL_OPERATOR)) {
            return $model;
        }

        throw new BadRequestHttpException('Оператор не найден.');
    }
}
