<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "directory_organization_form".
 *
 * @property integer $id
 * @property string $name
 * @property integer $is_separator
 * @property integer $is_active
 */
class DirectoryOrganizationForm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'directory_organization_form';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            [['is_separator', 'is_active'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public static function getList()
    {
        $query = static::find()
            ->where(['>', 'is_active', 0]);

        return $query->all();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'is_separator' => 'Is Separator',
            'is_active' => 'Is Active',
        ];
    }
}
