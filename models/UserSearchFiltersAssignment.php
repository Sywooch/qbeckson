<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_search_filters_assignment".
 *
 * @property integer $user_id
 * @property integer $filter_id
 * @property string $user_columns
 *
 * @property SettingsSearchFilters $filter
 * @property mixed $columns
 * @property User $user
 */
class UserSearchFiltersAssignment extends ActiveRecord
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
            [['user_columns'], 'safe'],
            [
                ['filter_id'],
                'exist', 'skipOnError' => true, 'targetClass' => SettingsSearchFilters::className(),
                'targetAttribute' => ['filter_id' => 'id']
            ],
            [
                ['user_id'],
                'exist', 'skipOnError' => true, 'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id']
            ],
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
            'columns' => 'Отображаемые данные',
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

    /**
     * @return array
     */
    public function getColumns()
    {
        $arrayColumns = preg_split('/[\s*,\s*]*,+[\s*,\s*]*/', $this->user_columns);
        return array_combine($arrayColumns, $arrayColumns);
    }

    /**
     * @param $data
     */
    public function setColumns($data)
    {
        $this->user_columns = '';
        if (is_array($data)) {
            $this->user_columns = implode(',', $data);
        }
    }

    /**
     * @param $filter
     * @return array|null|ActiveRecord|static
     */
    public static function findByFilter($filter)
    {
        if (null === $filter) {
            throw new \DomainException('Filter must be not empty!');
        }

        $query = static::find()
            ->andWhere([
                'filter_id' => $filter->id,
                'user_id' => Yii::$app->user->id,
            ]);

        if (!$userFilter = $query->one()) {
            $userFilter = new static([
                'filter_id' => $filter->id,
                'user_id' => Yii::$app->user->id,
                'user_columns' => implode(',', $filter->columnsForUser),
            ]);
            $userFilter->save();
        }

        return $userFilter;
    }
}
