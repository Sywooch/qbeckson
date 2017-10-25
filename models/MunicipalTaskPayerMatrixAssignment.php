<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "municipal_task_payer_matrix_assignment".
 *
 * @property integer $payer_id
 * @property integer $matrix_id
 * @property integer $can_be_chosen
 * @property integer $number
 * @property integer $number_type
 *
 * @property Payers $payer
 * @property MunicipalTaskMatrix $matrix
 */
class MunicipalTaskPayerMatrixAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'municipal_task_payer_matrix_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payer_id', 'matrix_id'], 'required'],
            [['payer_id', 'matrix_id', 'can_be_chosen', 'number', 'number_type'], 'integer'],
            [['payer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payers::className(), 'targetAttribute' => ['payer_id' => 'id']],
            [['matrix_id'], 'exist', 'skipOnError' => true, 'targetClass' => MunicipalTaskMatrix::className(), 'targetAttribute' => ['matrix_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'payer_id' => 'Payer ID',
            'matrix_id' => 'Matrix ID',
            'can_be_chosen' => 'Can Be Chosen',
            'number' => 'Number',
            'number_type' => 'Number Type',
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
    public function getMatrix()
    {
        return $this->hasOne(MunicipalTaskMatrix::className(), ['id' => 'matrix_id']);
    }
}
