<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "previus".
 *
 * @property integer $id
 * @property integer $certificate_id
 * @property integer $year_id
 * @property integer $organization_id
 * @property integer $program_id
 *
 * @property Years $year
 * @property Certificates $certificate
 * @property Organization $organization
 * @property Programs $program
 */
class Previus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'previus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['certificate_id', 'year_id', 'organization_id', 'program_id'], 'required'],
            [['certificate_id', 'year_id', 'organization_id', 'program_id', 'actual'], 'integer'],
            [['year_id'], 'exist', 'skipOnError' => true, 'targetClass' => Years::className(), 'targetAttribute' => ['year_id' => 'id']],
            [['certificate_id'], 'exist', 'skipOnError' => true, 'targetClass' => Certificates::className(), 'targetAttribute' => ['certificate_id' => 'id']],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Programs::className(), 'targetAttribute' => ['program_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'certificate_id' => 'ID Ребенка',
            'year_id' => 'Year ID',
            'organization_id' => 'ID Организации',
            'program_id' => 'ID Программы',
            'actual' => 'Актуальность'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getYear()
    {
        return $this->hasOne(Years::className(), ['id' => 'year_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificate()
    {
        return $this->hasOne(Certificates::className(), ['id' => 'certificate_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'organization_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Programs::className(), ['id' => 'program_id']);
    }
}
