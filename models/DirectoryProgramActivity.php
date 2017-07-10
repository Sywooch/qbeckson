<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "directory_program_activity".
 *
 * @property integer $id
 * @property integer $direction_id
 * @property integer $user_id
 * @property string $name
 * @property integer $status
 *
 * @property DirectoryProgramDirection $direction
 * @property User $user
 * @property ProgramActivityAssignment[] $programActivityAssignments
 * @property Programs[] $programs
 */
class DirectoryProgramActivity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'directory_program_activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['direction_id', 'name'], 'required'],
            [['direction_id', 'user_id', 'status'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'direction_id' => 'Direction ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(DirectoryProgramDirection::className(), ['id' => 'direction_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramActivityAssignments()
    {
        return $this->hasMany(ProgramActivityAssignment::className(), ['activity_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrograms()
    {
        return $this->hasMany(Programs::className(), ['id' => 'program_id'])->viaTable('program_activity_assignment', ['activity_id' => 'id']);
    }
}
