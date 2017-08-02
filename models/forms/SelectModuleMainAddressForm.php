<?php

namespace app\models\forms;

use app\components\GoogleCoordinates;
use app\models\Model;
use app\models\ProgrammeModule;
use app\models\ProgramModuleAddress;

/**
 * Class SelectModuleMainAddressForm
 * @package app\models\forms
 */
class SelectModuleMainAddressForm extends Model
{
    public $addressId;

    private $module;
    /** @var ProgramModuleAddress */
    private $moduleAddress;
    private $googleCoordinatesComponent;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['addressId', 'required'],
            ['addressId', 'integer'],
            ['addressId', 'validateAddress']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'addressId' => 'Основной адрес'
        ];
    }

    /**
     * Validate address
     */
    public function validateAddress()
    {
        if ($this->getModule()) {
            $moduleAddress = ProgramModuleAddress::find()
                ->andWhere([
                    'id' => $this->addressId,
                    'program_module_id' => $this->getModule()->id,
                ])
                ->one();
            if (null !== $moduleAddress) {
                $this->moduleAddress = $moduleAddress;

                return;
            }
        }

        $this->addError('addressId', 'Неправильный адрес.');
    }

    /**
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            if (null !== ($oldAddress = $this->getModule()->mainAddress)) {
                $oldAddress->status = 0;
                $oldAddress->lng = null;
                $oldAddress->lat = null;
                $oldAddress->save(false);
            }
            $this->setGoogleCoordinatesComponent($this->moduleAddress->address);

            $this->moduleAddress->status = 1;
            $this->moduleAddress->lat = $this->getGoogleCoordinatesComponent()->getLat();
            $this->moduleAddress->lng = $this->getGoogleCoordinatesComponent()->getLng();

            return $this->moduleAddress->save(false);
        }

        return false;
    }

    /**
     * @return ProgrammeModule
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param ProgrammeModule $module
     */
    public function setModule($module)
    {
        $this->module = $module;
        $this->addressId = $module->mainAddress ? $module->mainAddress->id : null;
    }

    /**
     * @return GoogleCoordinates
     */
    public function getGoogleCoordinatesComponent()
    {
        return $this->googleCoordinatesComponent;
    }

    /**
     * @param string $address
     */
    public function setGoogleCoordinatesComponent($address)
    {
        $this->googleCoordinatesComponent = new GoogleCoordinates($address);
    }
}
