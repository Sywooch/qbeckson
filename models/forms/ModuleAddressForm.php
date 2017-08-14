<?php

namespace app\models\forms;

use app\models\ProgrammeModule;
use yii\base\Model;

class ModuleAddressForm extends Model
{
    public $isChecked = [];
    public $addressIds = [];
    public $statuses = [];
    public $names = [];

    private $model;


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



        }

        return false;
    }

    /**
     * Load Model
     */
    private function loadModel()
    {

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
