<?php

namespace app\commands;

use yii;
use yii\console\Controller;
use yii\console\Exception;
use app\models\Operators;
use app\models\Coefficient;
use app\models\Mun;
use app\models\Payers;
use app\models\Organization;

class MergerController extends Controller
{
    public function actionSetOperator($operatorId = 1, $region = 14)
    {
        $operator = $this->findOperator($operatorId);
        if (empty($operator->region)) {
            $operator->setRegion($region);
        }

        Coefficient::updateAll(['operator_id' => $operator->id], ['operator_id' => null]);
        Mun::updateAll(['operator_id' => $operator->id], ['operator_id' => null]);
        Payers::updateAll(['operator_id' => $operator->id], ['operator_id' => null]);

        $organizations = Organization::findWithoutOperator($operator->id);
        foreach ($organizations as $organization) {
            $operator->link('organizations', $organization);
        }

        return Controller::EXIT_CODE_ERROR;
    }

    protected function findOperator($id)
    {
        if (($model = Operators::findOne($id)) !== null) {
            return $model;
        } else {
            throw new Exception('Operator does not exist.');
        }
    }
}
