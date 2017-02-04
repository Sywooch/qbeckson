<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "informs".
 *
 * @property integer $id
 * @property integer $program_id
 * @property integer $contract_id
 * @property integer $from
 * @property string $text
 * @property string $date
 * @property integer $read
 *
 * @property Programs $program
 * @property Contracts $contract
 */
class Informs extends \yii\db\ActiveRecord
{
    public $dop;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'informs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_id'], 'required'],
            [['program_id', 'contract_id', 'from', 'read', 'status'], 'integer'],
            [['text', 'dop'], 'string'],
            [['date'], 'safe'],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Programs::className(), 'targetAttribute' => ['program_id' => 'id']],
            [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contracts::className(), 'targetAttribute' => ['contract_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_id' => 'ID программы',
            'contract_id' => 'ID договора',
            'from' => 'Для',
            'text' => 'Сообщение',
            'date' => 'Дата',
            'read' => 'Прочитано',
            'dop' => 'Причина отказа',
            'status' => 'Статус',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Programs::className(), ['id' => 'program_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Contracts::className(), ['id' => 'contract_id']);
    }


    public function getOperatorInforms() {

        $query = Informs::find();

        if(!Yii::$app->user->isGuest) {
            $query->where(['from' => 1]);
        }

        return $query->one();
    }
}
