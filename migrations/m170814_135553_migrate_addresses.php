<?php

use app\models\Organization;
use app\models\OrganizationAddress;
use app\models\ProgramAddressAssignment;
use app\models\ProgramModuleAddressAssignment;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

class m170814_135553_migrate_addresses extends Migration
{
    /**
     * Обновление адресов организаций
     */
    public function safeUp()
    {
        $organizations = Organization::find()->all();
        $addresses = [];
        //Формируем адреса
        foreach ($organizations as $organization) {
            $programs = $organization->programs;
            if (!empty($programs)) {
                foreach ($programs as $program) {
                    $modules = $program->modules;
                    if (!empty($modules)) {
                        foreach ($modules as $module) {
                            $moduleAddresses = $module->oldAddresses;
                            if (!empty($moduleAddresses)) {
                                $moduleAddresses = ArrayHelper::toArray($moduleAddresses);
                                $addresses[$organization->id][$program->id][$module->id] = $moduleAddresses;
                            }
                            unset($moduleAddresses);
                        }
                    }
                }
            }
        }

        //Добавляем в базу
        $googleGeoComponent = new \app\components\GoogleCoordinates();
        /**
         * @var integer $organizationId
         * @var array $organization
         */
        foreach ($addresses as $organizationId => $organization) {
            $organizationStatus = 0;
            /**
             * @var integer $programId
             * @var array $program
             */
            foreach ($organization as $programId => $program) {
                $programStatus = 0;
                /**
                 * @var integer $moduleId
                 * @var array $module
                 */
                foreach ($program as $moduleId => $module) {
                    $moduleStatus = 0;
                    /**
                     * @var array $address
                     * @var array $module
                     */
                    foreach ($module as $address) {
                        $googleGeoComponent->setAddress($address['address']);
                        $organizationAddress = new OrganizationAddress([
                            'organization_id' => $organizationId,
                            'address' => $address['address'],
                            'lat' => $googleGeoComponent->getLat(),
                            'lng' => $googleGeoComponent->getLng(),
                            'status' => $organizationStatus === 0 ? $address['status'] : 0,
                        ]);
                        if ($address['status'] === 1) {
                            $organizationStatus = $address['status'];
                        }
                        try {
                            $organizationAddress->save(false);
                        } catch (\Exception $e) {
                            $organizationAddress = OrganizationAddress::findOne([
                                'organization_id' => $organizationId,
                                'address' => $address['address'],
                            ]);
                        }

                        $programAddress = new ProgramAddressAssignment([
                            'program_id' => $programId,
                            'organization_address_id' => $organizationAddress->id,
                            'status' => $programStatus === 0 ? $address['status'] : 0,
                        ]);
                        if ($address['status'] === 1) {
                            $programStatus = $address['status'];
                        }
                        try {
                            $programAddress->save(false);
                        } catch (\Exception $e) {
                            $programAddress = ProgramAddressAssignment::findOne([
                                'program_id' => $programId,
                                'organization_address_id' => $organizationAddress->id,
                            ]);
                        }

                        $moduleAddress = new ProgramModuleAddressAssignment([
                            'program_module_id' => $moduleId,
                            'program_address_assignment_id' => $programAddress->id,
                            'status' => $moduleStatus === 0 ? $address['status'] : 0
                        ]);
                        if ($address['status'] === 1) {
                            $moduleStatus = $address['status'];
                        }
                        try {
                            $moduleAddress->save(false);
                        } catch (\Exception $e) {
                        }

                        unset($organizationAddress, $programAddress, $moduleAddress);
                    }
                }
            }
        }
    }

    public function safeDown()
    {
        return true;
    }
}
