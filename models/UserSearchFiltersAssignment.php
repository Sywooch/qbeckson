<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_search_filters_assignment".
 *
 * @property integer $user_id
 * @property integer $filter_id
 * @property string $user_columns
 *
 * @property SettingsSearchFilters $filter
 * @property User $user
 */
class UserSearchFiltersAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_search_filters_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'filter_id'], 'required'],
            [['user_id', 'filter_id'], 'integer'],
            [['user_columns'], 'string'],
            [['filter_id'], 'exist', 'skipOnError' => true, 'targetClass' => SettingsSearchFilters::className(), 'targetAttribute' => ['filter_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'filter_id' => 'Filter ID',
            'user_columns' => 'Отображаемые данные',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilter()
    {
        return $this->hasOne(SettingsSearchFilters::className(), ['id' => 'filter_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
