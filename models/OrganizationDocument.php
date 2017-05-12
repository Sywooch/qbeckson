<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "organization_document".
 *
 * @property integer $id
 * @property integer $organization_id
 * @property integer $type
 * @property string $filename
 * @property integer $created_at
 */
class OrganizationDocument extends \yii\db\ActiveRecord
{
    const TYPE_COMMON = 10;

    const TYPE_LICENSE = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'organization_document';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['organization_id', 'type', 'filename'], 'required'],
            [['organization_id', 'type', 'created_at'], 'integer'],
            [['filename'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'organization_id' => 'Organization ID',
            'type' => 'Тип файла',
            'filename' => 'Файл',
            'created_at' => 'Загружен',
        ];
    }
}
