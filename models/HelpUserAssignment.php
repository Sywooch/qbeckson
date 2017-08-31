<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "help_user_assignment".
 *
 * @property integer $help_id
 * @property integer $user_id
 * @property integer $status
 *
 * @property Help $help
 * @property User $user
 */
class HelpUserAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'help_user_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['help_id', 'user_id'], 'required'],
            [['help_id', 'user_id', 'status'], 'integer'],
            [['help_id'], 'exist', 'skipOnError' => true, 'targetClass' => Help::className(), 'targetAttribute' => ['help_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'help_id' => 'Help ID',
            'user_id' => 'User ID',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHelp()
    {
        return $this->hasOne(Help::className(), ['id' => 'help_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
