<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "municipal_task_matrix".
 *
 * @property integer $id
 * @property string $name
 * @property integer $can_choose_pf
 * @property integer $can_choose_ac
 * @property integer $can_set_numbers_pf
 * @property integer $can_set_numbers_ac
 *
 * @property MunicipalTaskPayerMatrixAssignment[] $municipalTaskPayerMatrixAssignments
 * @property Payers[] $payers
 */
class MunicipalTaskMatrix extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'municipal_task_matrix';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['can_choose_pf', 'can_choose_ac', 'can_set_numbers_pf', 'can_set_numbers_ac'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'can_choose_pf' => 'Can Choose Pf',
            'can_choose_ac' => 'Can Choose Ac',
            'can_set_numbers_pf' => 'Can Set Numbers Pf',
            'can_set_numbers_ac' => 'Can Set Numbers Ac',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipalTaskPayerMatrixAssignments()
    {
        return $this->hasMany(MunicipalTaskPayerMatrixAssignment::className(), ['matrix_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayers()
    {
        return $this->hasMany(Payers::className(), ['id' => 'payer_id'])->viaTable('municipal_task_payer_matrix_assignment', ['matrix_id' => 'id']);
    }
}
