<?php

namespace app\models\mailing\activeRecord;

/**
 * This is the model class for table "{{%mailing_attachment}}".
 *
 * @property integer $id
 * @property integer $mailing_list_id
 * @property string $local_file_name
 * @property string $original_file_name
 *
 * @property MailingList $mailingList
 */
class MailingAttachment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mailing_attachment}}';
    }

    /**
     * @inheritdoc
     * @return MailingAttachmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MailingAttachmentQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mailing_list_id', 'local_file_name', 'original_file_name'], 'required'],
            [['mailing_list_id'], 'integer'],
            [['local_file_name', 'original_file_name'], 'string', 'max' => 255],
            [
                ['mailing_list_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => MailingList::className(),
                'targetAttribute' => ['mailing_list_id' => 'id']
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
            'mailing_list_id' => 'Mailing List ID',
            'local_file_name' => 'Local File Name',
            'original_file_name' => 'Original File Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingList()
    {
        return $this->hasOne(MailingList::className(), ['id' => 'mailing_list_id'])->inverseOf('mailingAttachments');
    }
}
