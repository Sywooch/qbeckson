<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "completeness".
 *
 * @property integer $id
 * @property integer $group_id
 * @property integer $month
 * @property integer $year
 * @property integer $completeness
 *
 * @property Groups $group
 */
class Completeness extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'completeness';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['month', 'year', 'completeness'], 'required'],
            [['group_id', 'contract_id', 'month', 'year', 'preinvoice'], 'integer'],
            [['completeness'], 'integer', 'max' => 100],
            [['sum'], 'number'],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Groups::className(), 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group_id' => 'ID Группы',
            'month' => 'Месяц',
            'year' => 'Год',
            'completeness' => 'Полнота услуг оказанных организацией',
            'preinvoice' => 'Предоплата',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Groups::className(), ['id' => 'group_id']);
    }

    public static function findPreinvoiceByContract($contractId, $month = null, $year = null)
    {
        $query = static::find()
            ->where([
                'preinvoice' => 1,
                'contract_id' => $contractId,
            ]);

        $query->andFilterWhere([
            'month' => $month,
            'year' => $year,
        ]);

        return $query->one();
    }
}
