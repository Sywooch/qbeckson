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
 * @property string $path
 * @property string $base_url
 * @property integer $created_at
 */
class OrganizationDocument extends \yii\db\ActiveRecord
{
    const TYPE_COMMON = 10;
    const TYPE_LICENSE = 20;
    const TYPE_CHARTER = 30;
    const TYPE_STATEMENT = 40;

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
            [['path', 'base_url'], 'string', 'max' => 50],
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

    /**
     * @return string
     */
    public function getUrl()
    {
        if (null === $this->path && null === $this->base_url && !empty($this->filename)) {
            return '/web/uploads/' . $this->filename;
        }

        return $this->base_url . '/' . $this->path;
    }
}
