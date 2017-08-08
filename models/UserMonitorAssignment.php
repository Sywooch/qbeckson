<?php

namespace app\models;

use Yii;
use app\behaviors\ArrayOrStringBehavior;

/**
 * This is the model class for table "user_monitor_assignment".
 *
 * @property integer $user_id
 * @property integer $monitor_id
 *
 * @property User $monitor
 * @property User $user
 */
class UserMonitorAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_monitor_assignment';
    }

    public function behaviors()
    {
        return [
            'array2string' => [
                'class' => ArrayOrStringBehavior::className(),
                'attributes1' => ['access_rights'],
                'attributes2' => ['access_rights'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'monitor_id'], 'required'],
            [['user_id', 'monitor_id'], 'integer'],
            [['monitor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['monitor_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['access_rights', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'monitor_id' => 'Monitor ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonitor()
    {
        return $this->hasOne(User::className(), ['id' => 'monitor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
