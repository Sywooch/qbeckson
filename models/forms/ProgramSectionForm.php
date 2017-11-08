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
    const SCENARIO_REFUSE = 'refuse';

    public $section;

    public $refuse_reason;

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
            [['refuse_reason'], 'string'],
            ['refuse_reason', 'required', 'on' => self::SCENARIO_REFUSE],
        ];
    }

    public function attributeLabels()
    {
        return [
            'section' => 'Раздел муниципального задания',
            'refuse_reason' => 'Причина отказа',
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_REFUSE] = $scenarios[self::SCENARIO_DEFAULT];

        return $scenarios;
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
     * @return bool
     */
    public function refuse(): bool
    {
        if ($this->getModel() && $this->validate()) {
            $this->model->refuse_reason = $this->refuse_reason;
            $this->model->verification = Programs::VERIFICATION_DENIED;
            $this->model->open = 0;
            if ($this->model->save(false, ['refuse_reason', 'verification', 'open'])) {
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
        $this->section = $model->municipal_task_matrix_id;
    }
}
