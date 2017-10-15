<?php

namespace app\components;


use app\models\Model;
use yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * Class SingleModelActor
 * @package app\components
 *
 * @property string $firstErrorAsString
 * @property yii\db\ActiveRecord $targetModel
 */
abstract class SingleModelActions extends Model
{
    public $_targetModel;


    /**
     * CertificateActions constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!is_array($config)) {
            parent::__construct(['targetModel' => $config]);

            return;
        }
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'targetModel' => 'Модель',
        ]);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['targetModel'], 'required'],
        ];
    }

    /**
     * @return yii\db\ActiveRecord|null
     */
    public function getTargetModel()
    {
        return $this->_targetModel;
    }

    /**
     *
     * @param yii\db\ActiveRecord ||integer $targetModel
     *
     * @throws InvalidParamException
     *
     */
    public function setTargetModel($targetModel)
    {
        if (is_a($targetModel, static::getTargetModelClass())) {
            $this->_targetModel = $targetModel;
        } elseif (is_scalar($targetModel)) {
            $this->_targetModel = call_user_func([static::getTargetModelClass(), 'findOne'], ['id' => $targetModel]);
        } else {
            throw new InvalidParamException('Параметр должен быть экземпляром ' .
                static::getTargetModelClass() .
                ' либо целым числом (идентификатором)');
        }
    }

    /** класс модели над которой производятся действия */
    public abstract static function getTargetModelClass(): string;

    /**
     * @param bool $validate
     *
     * @return bool
     * @throws yii\db\Exception
     */
    public function save($validate = true)
    {
        if ($validate && (!$this->validate() || !$this->targetModel->validate())) {
            return false;
        }
        $trans = Yii::$app->db->beginTransaction();
        $rollback = function () use ($trans)
        {
            $trans->rollBack();

            return false;
        };

        if ($this->saveActions($rollback, $validate) && $this->targetModel->save($validate)) {
            $trans->commit();

            return true;
        }

        return $rollback();


    }

    /**
     * Все манипуляции внутри этой функции происходят в трансзакции, можно прервать трансзакцию из нутри.
     * для успешного завершения вернуть true
     *
     * @param \Closure $transactionTerminator
     * @param bool $validate
     *
     * @return bool
     */
    public abstract function saveActions(\Closure $transactionTerminator, bool $validate): bool;
}