<?php

namespace app\models\forms;

use app\models\Programs;
use yii\base\Model;

/**
 * Class ProgramSectionForm
 * @package app\models\forms
 */
class ProgramSectionForm extends Model
{
    public $section;

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
            [['section'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'section' => 'Раздел муниципального задания',
        ];
    }


    /**
     * @return bool
     */
    public function save(): bool
    {
        if ($this->getModel() && $this->validate()) {
            $this->model->municipal_task_section = $this->section;
            if ($this->model->save(fasle, ['municipal_task_section'])) {
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
        $this->section = $model->municipal_task_section;
    }
}
