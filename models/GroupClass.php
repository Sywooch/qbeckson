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
 * @property integer $address_id
 *
 * @property Groups $group
 */
class GroupClass extends ActiveRecord
{
    public $dateStatus;

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
            [['group_id', 'hours_count', 'dateStatus', 'address_id', 'classroom'], 'integer'],
            [['week_day'], 'string', 'max' => 20],
            [['time_from', 'time_to'], 'string', 'max' => 10],
            [
                ['group_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Groups::class, 'targetAttribute' => ['group_id' => 'id']
            ],
            [
                ['address_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ProgramModuleAddress::class, 'targetAttribute' => ['address_id' => 'id']
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
        ];
    }

    /**
     * @return array
     */
    public static function weekDays()
    {
        return [
            'Понедельник' => 'Понедельник',
            'Вторник' => 'Вторник',
            'Среда' => 'Среда',
            'Четверг' => 'Четверг',
            'Пятница' => 'Пятница',
            'Суббота' => 'Суббота',
            'Воскресенье' => 'Воскресенье',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Groups::class, ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(ProgramModuleAddress::class, ['id' => 'address_id']);
    }
}
