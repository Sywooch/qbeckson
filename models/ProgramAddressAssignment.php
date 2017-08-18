<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "program_address_assignment".
 *
 * @property integer $id
 * @property integer $organization_address_id
 * @property integer $program_id
 * @property integer $status
 *
 * @property OrganizationAddress $address
 */
class ProgramAddressAssignment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'program_address_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['organization_address_id', 'program_id'], 'required'],
            [['organization_address_id', 'program_id', 'status'], 'integer'],
            [
                ['organization_address_id', 'program_id'], 'unique',
                'targetAttribute' => ['organization_address_id', 'program_id'],
                'message' => 'The combination of Organization Address ID and Program ID has already been taken.'
            ],
            [
                ['organization_address_id'], 'exist', 'skipOnError' => true,
                'targetClass' => OrganizationAddress::class,
                'targetAttribute' => ['organization_address_id' => 'id']
            ],
            [
                ['program_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Programs::class,
                'targetAttribute' => ['program_id' => 'id']
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(OrganizationAddress::class, ['id' => 'organization_address_id']);
    }
}
