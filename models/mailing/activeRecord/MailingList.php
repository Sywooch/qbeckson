<?php

namespace app\models\mailing\activeRecord;

use app\models\User;

/**
 * This is the model class for table "{{%mailing_list}}".
 *
 * @property integer $id
 * @property integer $created_by
 * @property integer $created_at
 * @property string $subject
 * @property string $message
 *
 * @property MailTask[] $mailTasks
 * @property MailingAttachment[] $mailingAttachments
 * @property User $createdBy
 */
class MailingList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mailing_list}}';
    }

    /**
     * @inheritdoc
     * @return MailingListQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MailingListQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'created_at', 'subject', 'message'], 'required'],
            [['created_by', 'created_at'], 'integer'],
            [['message'], 'string'],
            [['subject'], 'string', 'max' => 40],
            [['created_by'], 'exist', 'skipOnError' => true,
                'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_by' => 'Автор',
            'created_at' => 'Создано',
            'subject' => 'Тема письма',
            'message' => 'Сообщение',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailTasks()
    {
        return $this->hasMany(MailTask::className(), ['mailing_list_id' => 'id'])->inverseOf('mailingList');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingAttachments()
    {
        return $this->hasMany(MailingAttachment::className(), ['mailing_list_id' => 'id'])->inverseOf('mailingList');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
}
