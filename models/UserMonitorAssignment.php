<?php

namespace app\models;

use Yii;

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
