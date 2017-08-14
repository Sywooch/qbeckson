<?php

namespace app\models\forms;

use app\models\ProgramAddressAssignment;
use app\models\Programs;
use yii\base\Model;

/**
 * Class ConfirmRequestForm
 * @package app\models\forms
 */
class ProgramAddressesForm extends Model
{
    public $isChecked = [];
    public $addressIds = [];
    public $statuses = [];
    public $names = [];

    private $model;

    /**
     * ProgramAddressesForm constructor.
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
            [['isChecked', 'addressIds', 'statuses'], 'each', 'rule' => ['integer']],
            [['names'], 'each', 'rule' => ['string']],
        ];
    }

    /**
     * Немного кастыльно. Удаляет все записи перед тем, как записать.
     * Можно сделать какой-то diff.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->getModel() && $this->validate()) {
            ProgramAddressAssignment::deleteAll(['program_id' => $this->getModel()->id]);
            foreach ($this->isChecked as $key => $record) {
                if ((int)$record === 1) {
                    $model = new ProgramAddressAssignment([
                        'organization_address_id' => $this->addressIds[$key],
                        'program_id' => $this->getModel()->id,
                        'status' => $this->statuses[$key],
                    ]);
                    if (false === $model->save(false)) {
                        return false;
                    }
                    unset($model);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @return Programs
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Programs $model
     */
    public function setModel($model)
    {
        $this->model = $model;
        $this->loadModel();
    }

    /**
     * Load Model
     */
    private function loadModel()
    {
        if (null === ($model = $this->getModel())) {
            throw new \DomainException('Model must be set');
        }
        foreach ($model->organization->addresses as $key => $address) {
            foreach ($model->addressAssignments as $assignment) {
                if ($assignment->organization_address_id === $address->id) {
                    $this->isChecked[$key] = 1;
                    $this->statuses[$key] = $assignment->status;
                }
            }
            $this->addressIds[$key] = $address->id;
            $this->names[$key] = $address->address;
        }
    }
}
