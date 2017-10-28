<?php

namespace app\models\mailing\activeRecord;

use app\models\User;
use Yii;

/**
 * This is the model class for table "{{%mail_task}}".
 *
 * @property integer $id
 * @property integer $mailing_list_id
 * @property integer $status 1 - created; 10 - inQueue  30 - finish; 40 - has errors;
 * @property integer $target_user_id
 * @property integer $updated_at
 * @property string $log_message
 * @property string $email
 * @property integer $target_type
 *
 * @property MailingList $mailingList
 * @property User $targetUser
 */
class MailTask extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mail_task}}';
    }

    /**
     * @inheritdoc
     * @return MailTaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MailTaskQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mailing_list_id', 'status', 'target_user_id', 'updated_at', 'email', 'target_type'], 'required'],
            [['mailing_list_id', 'status', 'target_user_id', 'updated_at', 'target_type'], 'integer'],
            [['log_message', 'email'], 'string', 'max' => 255],
            [['mailing_list_id'], 'exist', 'skipOnError' => true,
                'targetClass' => MailingList::className(), 'targetAttribute' => ['mailing_list_id' => 'id']],
            [['target_user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::className(), 'targetAttribute' => ['target_user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'mailing_list_id' => Yii::t('app', 'Mailing List ID'),
            'status' => Yii::t('app', 'Status'),
            'target_user_id' => Yii::t('app', 'Target User ID'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'log_message' => Yii::t('app', 'Log Message'),
            'email' => Yii::t('app', 'Email'),
            'target_type' => Yii::t('app', 'Target Type'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingList()
    {
        return $this->hasOne(MailingList::className(), ['id' => 'mailing_list_id'])->inverseOf('mailTasks');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTargetUser()
    {
        return $this->hasOne(User::className(), ['id' => 'target_user_id']);
    }
}
