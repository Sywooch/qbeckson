<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cert_group".
 *
 * @property integer $id
 * @property integer $payer_id
 * @property string $group
 * @property integer $nominal
 *
 * @property Payers $payer
 * @property Certificates[] $certificates
 */
class CertGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cert_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payer_id', 'group', 'nominal'], 'required'],
            [['payer_id', 'is_special'], 'integer'],
            [['nominal'], 'integer', 'max' => 100000],
            [['group'], 'string', 'max' => 255],
            [['payer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payers::className(), 'targetAttribute' => ['payer_id' => 'id']],
        ];
    }

    public static function getActiveList($payerId)
    {
        $query = static::find()
            ->where(['payer_id' => $payerId])
            ->andWhere(['or', ['>', 'nominal', 0], ['=', 'is_special', 1]]);
        //print_r($query->asArray()->all());exit;

        return $query->all();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'payer_id' => 'Payer ID',
            'group' => 'Группа',
            'nominal' => 'Номинал',
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
    public function getCertificates()
    {
        return $this->hasMany(Certificates::className(), ['cert_group' => 'id']);
    }
}
