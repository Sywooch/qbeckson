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
            [['table_columns', 'inaccessible_columns'], 'safe'],
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
            'inaccessible_columns' => 'Атрибуты, которые невозможно выключить',
            'is_active' => 'Активно',
        ];
    }

    public function getTableColumns()
    {
        return preg_split('/[\s*,\s*]*,+[\s*,\s*]*/', $this->table_columns);
    }

    public function getInaccessibleColumns()
    {
        return preg_split('/[\s*,\s*]*,+[\s*,\s*]*/', $this->inaccessible_columns);
    }

    public function getColumnsForUser()
    {
        return array_diff($this->tableColumns, $this->inaccessibleColumns);
    }

    public static function findByTable($tableName)
    {
        $query = static::find()
            ->where(['>', 'is_active', 0])
            ->andWhere(['table_name' => $tableName]);

        return $query->one();
    }
}
