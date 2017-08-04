<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "program_module_address".
 *
 * @property integer $id
 * @property integer $program_module_id
 * @property string $address
 * @property string $lat
 * @property string $lng
 * @property integer $status
 *
 * @property Years $programModule
 */
class ProgramModuleAddress extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'program_module_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['address', 'required'],
            [['program_module_id', 'status'], 'integer'],
            [['address'], 'string', 'max' => 255],
            [['lat', 'lng'], 'string', 'max' => 25],
            [
                ['program_module_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Years::class, 'targetAttribute' => ['program_module_id' => 'id']
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
            'program_module_id' => 'Program Module ID',
            'address' => 'Адрес',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'status' => 'Использовать, как основной адрес',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramModule()
    {
        return $this->hasOne(Years::class, ['id' => 'program_module_id']);
    }
}
