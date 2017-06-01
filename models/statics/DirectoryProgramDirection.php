<?php

namespace app\models\statics;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "directory_program_direction".
 *
 * @property integer $id
 * @property string $name
 *
 * @property \yii\db\ActiveQuery $directoryProgramActivities
 */
class DirectoryProgramDirection extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'directory_program_direction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirectoryProgramActivities()
    {
        return $this->hasMany(DirectoryProgramActivity::class, ['direction_id' => 'id']);
    }

    /**
     * @return static[]|null
     */
    public static function findAllRecords()
    {
        return static::find()->all();
    }
}
