<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "organization_payer_assignment".
 *
 * @property integer $organization_id
 * @property integer $payer_id
 * @property integer $status
 *
 * @property Payers $payer
 * @property Organization $organization
 */
class OrganizationPayerAssignment extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;

    const STATUS_PENDING = 20;

    const STATUS_REFUSED = 30;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'organization_payer_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['organization_id', 'payer_id'], 'required'],
            [['organization_id', 'payer_id', 'status'], 'integer'],
            [['payer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payers::className(), 'targetAttribute' => ['payer_id' => 'id']],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'organization_id' => 'Organization ID',
            'payer_id' => 'Payer ID',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayer()
    {
        return $this->hasOne(Payers::className(), ['id' => 'payer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'organization_id']);
    }

    public function getStatuses() {
        return [
            self::STATUS_ACTIVE => 'Организация подведомствена вам',
            self::STATUS_PENDING => 'Ожидает подтверждения подведомственности',
            self::STATUS_REFUSED => 'Запрос на подведомственность отклонен',
        ];
    }
}
