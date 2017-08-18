<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "organization_address".
 *
 * @property integer $id
 * @property integer $organization_id
 * @property string $address
 * @property string $lat
 * @property string $lng
 * @property integer $status
 *
 * @property Organization $organization
 * @property ProgramAddressAssignment[] $programAddressAssignments
 * @property Programs[] $programs
 */
class OrganizationAddress extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'organization_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address', 'organization_id'], 'required'],
            [['organization_id', 'status'], 'integer'],
            [['address'], 'string', 'max' => 255],
            [['lat', 'lng'], 'safe'],
            [
                ['organization_id', 'address'], 'unique',
                'targetAttribute' => ['organization_id', 'address'],
                'message' => 'Вы уже указали такой адрес.'
            ],
            [
                ['organization_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Organization::class,
                'targetAttribute' => ['organization_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address' => 'Адрес',
            'status' => 'Статус',
            'lat' => 'Lat',
            'lng' => 'Lng',
        ];
    }
}
