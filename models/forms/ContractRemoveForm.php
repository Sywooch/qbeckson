<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;

/**
 * Class ContractRemoveForm
 * @package app\models\forms
 */
class ContractRemoveForm extends Model
{
    public $organizationId;
    public $contractIdStart;
    public $contractIdFinish;


    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['organizationId', 'contractIdStart', 'contractIdFinish'], 'required'],
            [['organizationId', 'contractIdStart', 'contractIdFinish'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'organizationId' => 'ID организации',
            'contractIdStart' => 'Начальный ID контракта',
            'contractIdFinish' => 'Конечный ID контракта',
        ];
    }
}
