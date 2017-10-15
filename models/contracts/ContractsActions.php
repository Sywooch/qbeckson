<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 13.10.2017
 * Time: 20:24
 */

namespace app\models\contracts;


use app\components\SingleModelActions;
use app\models\Contracts;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class ContractsActions
 *
 * @package app\models\contracts
 *
 * @property Contracts $contract
 */
abstract class ContractsActions extends SingleModelActions
{
    /**
     * @return string
     */
    public static function getTargetModelClass(): string
    {
        return Contracts::className();
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'contract' => 'Договор',
        ]);
    }

    /**
     * @return ActiveRecord|null
     */
    public function getContract()
    {
        return $this->targetModel;
    }

    /**
     *
     * @param Certificates||integer $certificate
     *
     * @throws InvalidParamException
     *
     */
    public function setContract($group)
    {
        parent::setTargetModel($group);
    }
}