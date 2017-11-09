<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "municipal_task_contract".
 *
 * @property integer $id
 * @property integer $certificate_id
 * @property integer $payer_id
 * @property integer $program_id
 * @property integer $group_id
 * @property integer $status
 * @property integer $created_at
 *
 * @property Certificates $certificate
 * @property Groups $group
 * @property Payers $payer
 * @property Programs $program
 */
class MunicipalTaskContract extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 10;
    const STATUS_ACTIVE = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'municipal_task_contract';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['certificate_id', 'payer_id', 'organization_id', 'program_id', 'group_id', 'status', 'created_at'], 'integer'],
            [['number', 'pdf'], 'string'],
            [['certificate_id'], 'exist', 'skipOnError' => true, 'targetClass' => Certificates::className(), 'targetAttribute' => ['certificate_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Groups::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['payer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payers::className(), 'targetAttribute' => ['payer_id' => 'id']],
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
            'payer_id' => 'Payer ID',
            'program_id' => 'Program ID',
            'group_id' => 'Group ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'number' => 'Введите номер договора',
        ];
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
    public function getGroup()
    {
        return $this->hasOne(Groups::className(), ['id' => 'group_id']);
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Programs::className(), ['id' => 'program_id']);
    }
    
    public static function findByProgram($programId, $certificate)
    {
        $query = static::find()
            ->where([
                'program_id' => $programId,
                'certificate_id' => $certificate->id,
            ]);

        return $query->one();
    }

    public function approve()
    {
        $this->status = self::STATUS_ACTIVE;

        return $this->save(false, ['status', 'number', 'pdf']);
    }

    public static function getCountContracts($certificate, $matrixId = null, $status = null)
    {
        $query = static::find()
            ->joinWith('program')
            ->andFilterWhere([
                'certificate_id' => $certificate->id,
                'programs.municipal_task_matrix_id' => $matrixId,
                '`municipal_task_contract`.status' => $status,
            ]);

        return $query->count();
    }

    public function generatePdf()
    {
        $html = 'Тестовый договор на обучение по муниципальным заданиям.';

        $mpdf = new \mPDF();
        $mpdf->WriteHtml($html);
        $filename = 'task-' . $this->number . '_' . date('d_m_Y') . '_' . $this->organization_id . '.pdf';
        $mpdf->Output(Yii::getAlias('@pfdoroot/uploads/contracts/') . $filename, 'F');

        return $filename;
    }
}
