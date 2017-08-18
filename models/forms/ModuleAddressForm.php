<?php

namespace app\models\forms;

use app\models\ProgrammeModule;
use app\models\ProgramModuleAddressAssignment;
use yii\base\Model;

/**
 * Class ModuleAddressForm
 * @package app\models\forms
 */
class ModuleAddressForm extends Model
{
    public $isChecked = [];
    public $addressIds = [];
    public $statuses = [];
    public $names = [];

    private $model;

    /**
     * ModuleAddressForm constructor.
     * @param ProgrammeModule $model
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
            [['isChecked', 'addressIds', 'statuses'], 'each', 'rule' => ['integer']],
            [['names'], 'each', 'rule' => ['string']],
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if ($this->getModel() && $this->validate()) {
            foreach ($this->isChecked as $key => $record) {
                $model = ProgramModuleAddressAssignment::findOne([
                    'program_address_assignment_id' => $this->addressIds[$key],
                    'program_module_id' => $this->getModel()->id,
                ]);
                if ((int)$record === 1) {
                    if (null === $model) {
                        $model = new ProgramModuleAddressAssignment([
                            'program_address_assignment_id' => $this->addressIds[$key],
                            'program_module_id' => $this->getModel()->id,
                        ]);
                    }
                    $model->status = $this->statuses[$key];
                    if (false === $model->save(false)) {
                        return false;
                    }
                } else {
                    if (null !== $model) {
                        $model->delete();
                    }
                }
                unset($model);
            }

            return true;
        }

        return false;
    }

    /**
     * Load Model
     */
    private function loadModel()
    {
        if (null === ($model = $this->getModel())) {
            throw new \DomainException('Model must be set');
        }

        foreach ($model->program->addressAssignments as $key => $address) {
            foreach ($model->moduleAddressAssignments as $assignment) {
                if ($assignment->program_address_assignment_id === $address->id) {
                    $this->isChecked[$key] = 1;
                    $this->statuses[$key] = $assignment->status;
                }
            }
            $this->addressIds[$key] = $address->id;
            $this->names[$key] = $address->address->address;
        }
    }

    /**
     * @return ProgrammeModule
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param ProgrammeModule $model
     */
    public function setModel($model)
    {
        $this->model = $model;
        $this->loadModel();
    }
}
