<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "settings_search_filters".
 *
 * @property integer $id
 * @property string $table_name
 * @property string $table_columns
 * @property integer $is_active
 */
class SettingsSearchFilters extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings_search_filters';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['table_columns'], 'string'],
            [['is_active'], 'integer'],
            [['table_name'], 'string', 'max' => 255],
            [['table_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_name' => 'Название таблицы',
            'table_columns' => 'Атрибуты для поиска',
            'is_active' => 'Активно',
        ];
    }
}
