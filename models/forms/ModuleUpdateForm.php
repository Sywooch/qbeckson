<?php

namespace app\models\forms;

use app\models\ProgrammeModule;
use Yii;
use yii\base\Model;

/**
 * Class ModuleUpdateForm
 * @package app\models\forms
 */
class ModuleUpdateForm extends Model
{
    public $price;
    public $confirm;

    private $model;

    /**
     * ModuleUpdateForm constructor.
     * @param integer $moduleId
     * @param array $config
     */
    public function __construct($moduleId, $config = [])
    {
        $this->setModel($moduleId);
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['price', 'required'],
            ['confirm', 'required', 'requiredValue' => 1, 'message' => 'Необходимо подтвердить действие'],
            ['price', 'number'],
            ['price', 'validateData']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'price' => 'Установите цену модуля:',
            'confirm' => 'Подтвердить',
        ];
    }

    /**
     * @param $attribute
     */
    public function validateData($attribute)
    {
        if (count($this->getModel()->contracts) > 0) {
            $this->addError($attribute, 'Нельзя изменить цену программы, есть заявка на эту программу');
        }
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if ($this->validate() && null !== ($model = $this->getModel())) {
            $model->price = $this->price;

            return $model->save(false, ['price']);
        }

        return false;
    }

    /**
     * @return ProgrammeModule|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param integer $moduleId
     * @throws \DomainException
     */
    public function setModel($moduleId)
    {
        $this->model = ProgrammeModule::find()
            ->joinWith(['program'])
            ->andWhere([
                'years.id' => $moduleId,
                'programs.organization_id' => Yii::$app->user->getIdentity()->organization->id,
            ])
            ->one();
        if (null === $this->model) {
            throw new \DomainException('Model not found');
        }
        $this->price = $this->model->price ?: null;
    }



}
