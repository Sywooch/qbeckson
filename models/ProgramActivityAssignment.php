<?php

namespace app\models;

use Yii;
use app\models\statics\DirectoryProgramActivity;

/**
 * This is the model class for table "program_activity_assignment".
 *
 * @property integer $program_id
 * @property integer $activity_id
 *
 * @property DirectoryProgramActivity $activity
 * @property Programs $program
 */
class ProgramActivityAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'program_activity_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_id', 'activity_id'], 'required'],
            [['program_id', 'activity_id'], 'integer'],
            [['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => DirectoryProgramActivity::className(), 'targetAttribute' => ['activity_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Programs::className(), 'targetAttribute' => ['program_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'program_id' => 'Program ID',
            'activity_id' => 'Activity ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasOne(DirectoryProgramActivity::className(), ['id' => 'activity_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Programs::className(), ['id' => 'program_id']);
    }
}
