<?php

namespace app\components;

use Yii;
use app\models\UserIdentity;
use yii\base\Component;
use yii\web\BadRequestHttpException;
use app\models\Operators;

class Operator extends Component
{
    public function getIdentity()
    {
        $operatorId = $this->getOperator();

        if ($model = Operators::findOne($operatorId)) {
            return $model;
        }

        throw new BadRequestHttpException('Оператор не найден.');
    }

    private function getOperator()
    {
        if (Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)) {
            $operatorId = Yii::$app->user->identity->operator->id;
        } elseif (Yii::$app->user->can(UserIdentity::ROLE_PAYER)) {
            $operatorId = Yii::$app->user->identity->payer->operator_id;
        } elseif (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            $operatorId = Yii::$app->user->identity->organization->operator->id;
        } elseif (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
            $operatorId = Yii::$app->user->identity->certificate->payers->operator_id;
        }

        if (empty($operatorId)) {
            $operatorId = GLOBAL_OPERATOR;
        }

        return $operatorId;
    }
}
