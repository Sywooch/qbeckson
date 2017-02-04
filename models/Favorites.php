<?php

namespace app\models;

use Yii;
use app\models\Certificates;

/**
 * This is the model class for table "favorites".
 *
 * @property integer $id
 * @property integer $certificate_id
 * @property integer $program_id
 * @property integer $organization_id
 *
 * @property Organization $organization
 * @property Certificates $certificate
 * @property Programs $program
 */
class Favorites extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'favorites';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['certificate_id', 'program_id', 'organization_id', 'type'], 'required'],
            [['certificate_id', 'program_id', 'organization_id', 'type'], 'integer'],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
            [['certificate_id'], 'exist', 'skipOnError' => true, 'targetClass' => Certificates::className(), 'targetAttribute' => ['certificate_id' => 'id']],
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
            'certificate_id' => 'Certificate ID',
            'program_id' => 'Program ID',
            'organization_id' => 'Organization ID',
            'type' => 'type',
        ];
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
    public function getCertificate()
    {
        return $this->hasOne(Certificates::className(), ['id' => 'certificate_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Programs::className(), ['id' => 'program_id']);
    }

     public function getFavoritesProgram() {

        if(!Yii::$app->user->isGuest) {

            $certificates = new Certificates();
            $certificate = $certificates->getCertificates();

            $rows = (new \yii\db\Query())
                ->select(['program_id'])
                ->from('favorites')
                ->where(['certificate_id' => $certificate['id']])
                ->andWhere(['type' => 1])
                ->column();

            return $rows;
        }
    }
}
