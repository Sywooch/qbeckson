<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%group_class}}".
 *
 * @property integer $id
 * @property integer $group_id
 * @property string $week_day
 * @property integer $hours_count
 * @property string $time_from
 * @property string $time_to
 * @property integer $classroom
 * @property string $address
 *
 * @property Groups $group
 */
class GroupClass extends ActiveRecord
{
    public $status;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%group_class}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['address', 'required'],
            [['group_id', 'hours_count', 'status', 'classroom'], 'integer'],
            [['week_day'], 'string', 'max' => 20],
            [['address'], 'string', 'max' => 255],
            [['time_from', 'time_to'], 'string', 'max' => 10],
            [
                ['group_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Groups::class, 'targetAttribute' => ['group_id' => 'id']
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
            'group_id' => 'Group ID',
            'week_day' => 'День недели',
            'hours_count' => 'Количество часов',
            'time_from' => 'Время с',
            'time_to' => 'Время до',
            'status' => 'Статус',
            'classroom' => 'Номер кабинета',
            'address' => 'Адрес',
        ];
    }

    /**
     * @return array
     */
    public static function weekDays()
    {
        return [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            7 => 'Воскресенье',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Groups::class, ['id' => 'group_id']);
    }
}
