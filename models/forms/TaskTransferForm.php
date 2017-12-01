<?php

namespace app\models\forms;

use app\models\Programs;
use yii\base\Model;

/**
 * Class TaskTransferForm
 * @package app\models\forms
 */
class TaskTransferForm extends Model
{
    private $model;

    /**
     * ProgramSectionForm constructor.
     * @param Programs $model
     * @param array $config
     */
    public function __construct($model, $config = [])
    {
        $this->setModel($model);
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if ($this->getModel() && $this->validate()) {
            $this->model->municipal_task_matrix_id = $this->section;
            $this->model->verification = Programs::VERIFICATION_DONE;
            $this->model->open = 1;
            if ($this->model->save(false, ['municipal_task_matrix_id', 'verification', 'open'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Programs|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Programs $model
     */
    public function setModel(Programs $model)
    {
        $this->model = $model;
        //$this->section = $model->municipal_task_matrix_id;
    }
}
