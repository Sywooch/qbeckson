<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "disputes".
 *
 * @property integer $id
 * @property integer $contract_id
 * @property integer $month
 * @property integer $type
 * @property integer $from
 * @property string $text
 *
 * @property Contracts $contract
 */
class Disputes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'disputes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contract_id', 'type', 'user_id', 'display'], 'integer'],
            [['text'], 'string'],
            [['date'], 'safe'],
            [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contracts::className(), 'targetAttribute' => ['contract_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contract_id' => 'Contract ID',
            'date' => 'Дата',
            'type' => 'Type',
            'user_id' => 'Пользователь',
            'text' => 'Text',
            'display' => 'Не показывать организации',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Contracts::className(), ['id' => 'contract_id']);
    }
    
    public function getUsers()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
