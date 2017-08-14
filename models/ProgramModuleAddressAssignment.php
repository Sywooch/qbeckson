<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "program_module_address_assignment".
 *
 * @property integer $program_address_assignment_id
 * @property integer $program_module_id
 * @property integer $status
 *
 * @property Years $programModule
 * @property ProgramAddressAssignment $programAddressAssignment
 */
class ProgramModuleAddressAssignment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'program_module_address_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_address_assignment_id', 'program_module_id'], 'required'],
            [['program_address_assignment_id', 'program_module_id', 'status'], 'integer'],
            [
                ['program_address_assignment_id', 'program_module_id'], 'unique',
                'targetAttribute' => ['program_address_assignment_id', 'program_module_id'],
                'message' => 'The combination of Program Address ID and Program Module ID has already been taken.'
            ],
            [
                ['program_module_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ProgrammeModule::class,
                'targetAttribute' => ['program_module_id' => 'id']
            ],
            [
                ['program_address_assignment_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ProgramAddressAssignment::class,
                'targetAttribute' => ['program_address_assignment_id' => 'id']
            ],
        ];
    }
}
