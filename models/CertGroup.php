<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "cert_group".
 *
 * @property integer $id
 * @property integer $payer_id
 * @property string $group
 * @property integer $nominal
 * @property float $nominal_f
 * @property integer $amount
 *
 * @property Payers $payer
 * @property Certificates[] $certificates
 */
class CertGroup extends ActiveRecord
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
            [['payer_id', 'group', 'nominal', 'nominal_f', 'amount'], 'required'],
            [['payer_id', 'is_special'], 'integer'],
            [['nominal', 'nominal_f'], 'number', 'max' => 100000],
            [['group'], 'string', 'max' => 255],
            [
                ['payer_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Payers::class, 'targetAttribute' => ['payer_id' => 'id']
            ],
        ];
    }

    /**
     * @param $payerId
     * @return CertGroup[]|array|ActiveRecord[]
     */
    public static function getActiveList($payerId)
    {
        $query = static::find()
            ->where(['payer_id' => $payerId])
            ->andWhere(['or', ['>', 'nominal', 0], ['=', 'is_special', 1]]);

        return $query->all();
    }

    public static function getPossibleList($payerId)
    {
        $query = static::find()
            ->where(['payer_id' => $payerId])
            ->andWhere(['>', 'nominal', 0]);

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
            'nominal_f' => 'Номинал будущего периода (от ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_from) . ' до ' . Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_to) . ')',
            'countCertificates' => 'Количество используемых сертификатов',
            'amount' => 'Лимит',
        ];
    }

    public function hasVacancy()
    {
        $certGroupCount = Certificates::getCountCertGroup($this->id);

        if ($this->amount - $certGroupCount > 0) {
            return true;
        }

        return false;
    }

    public function getCountCertificates()
    {
        $query = Certificates::find()
            ->where(['cert_group' => $this->id]);

        return $query->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayer()
    {
        return $this->hasOne(Payers::class, ['id' => 'payer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificates()
    {
        return $this->hasMany(Certificates::class, ['cert_group' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificateGroupQueues()
    {
        return $this->hasMany(CertificateGroupQueue::className(), ['cert_group_id' => 'id']);
    }
}
