<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "certificate_group_queue".
 *
 * @property integer $certificate_id
 * @property integer $cert_group_id
 *
 * @property CertGroup $certGroup
 * @property Certificates $certificate
 */
class CertificateGroupQueue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'certificate_group_queue';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['certificate_id', 'cert_group_id'], 'required'],
            [['certificate_id', 'cert_group_id'], 'integer'],
            [['cert_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => CertGroup::className(), 'targetAttribute' => ['cert_group_id' => 'id']],
            [['certificate_id'], 'exist', 'skipOnError' => true, 'targetClass' => Certificates::className(), 'targetAttribute' => ['certificate_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'certificate_id' => 'Certificate ID',
            'cert_group_id' => 'Cert Group ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertGroup()
    {
        return $this->hasOne(CertGroup::className(), ['id' => 'cert_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificate()
    {
        return $this->hasOne(Certificates::className(), ['id' => 'certificate_id']);
    }
}
