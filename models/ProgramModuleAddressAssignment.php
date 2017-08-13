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
                'targetClass' => Years::className(),
                'targetAttribute' => ['program_module_id' => 'id']
            ],
            [
                ['program_address_assignment_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ProgramAddressAssignment::className(),
                'targetAttribute' => ['program_address_assignment_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'program_address_assignment_id' => 'Program Address Assignment ID',
            'program_module_id' => 'Program Module ID',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramModule()
    {
        return $this->hasOne(Years::className(), ['id' => 'program_module_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramAddressAssignment()
    {
        return $this->hasOne(ProgramAddressAssignment::className(), ['id' => 'program_address_assignment_id']);
    }
}
