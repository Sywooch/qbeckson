<?php

namespace app\components\periodicField;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * Class PeriodicFieldAR
 * @package app\components\periodicField
 *
 * @property
 *
 */
class PeriodicFieldAR extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%periodic_field}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => null,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['table_name', 'field_name'], 'required'],
            [['value'], 'string'],
            [['table_name', 'field_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_name' => 'Таблица',
            'field_name' => 'Поле',
            'created_at' => 'Дата',
            'created_by' => 'Пользователь',
            'value' => 'Значение',
        ];
    }
}
