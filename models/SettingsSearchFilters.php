<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "settings_search_filters".
 *
 * @property integer $id
 * @property string $table_name
 * @property string $table_columns
 * @property string $inaccessible_columns
 * @property string $role
 * @property array $inaccessibleColumns
 * @property array $columnsForUser
 * @property array $tableColumns
 * @property integer $is_active
 */
class SettingsSearchFilters extends ActiveRecord
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
            [['role'], 'required'],
            [['role'], 'string', 'max' => 50],
            [['table_columns', 'inaccessible_columns', 'type'], 'safe'],
            [['table_columns', 'inaccessible_columns'], 'uniqueColumnsValidator'],
            [['is_active'], 'integer'],
            [['table_name'], 'string', 'max' => 255],
            //[['table_name'], 'unique'],
            ['role', 'in', 'range' => array_keys(UserIdentity::roles())],
        ];
    }

    public function uniqueColumnsValidator($attribute, $params, \yii\validators\InlineValidator $validator)
    {
        $arr = $this->split($this->$attribute);
        if (count($arr) !== count(array_unique($arr))) {
            $this->addError($attribute, 'Столбцы должны быть уникальны!');
        }
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
            'role' => 'Роль',
        ];
    }

    /**
     * @return array
     */
    public function getTableColumns()
    {
        return $this->split($this->table_columns);
    }

    public function split(string $string): array
    {
        return preg_split('/[\s*,\s*]*,+[\s*,\s*]*/', $string);
    }

    /**
     * @return array
     */
    public function getInaccessibleColumns()
    {
        return $this->split($this->inaccessible_columns);
    }

    /**
     * @return array
     */
    public function getColumnsForUser()
    {
        return array_diff($this->getTableColumns(), $this->getInaccessibleColumns());
    }

    /**
     * @param $tableName
     * @return SettingsSearchFilters|array|ActiveRecord
     */
    public static function findByTable($tableName)
    {
        $query = static::find()
            ->andWhere(['>', 'is_active', 0])
            ->andWhere(['table_name' => $tableName]);

        return $query->one();
    }
}
