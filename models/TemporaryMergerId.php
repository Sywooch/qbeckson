<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "temporary_merger_id".
 *
 * @property integer $id
 * @property string $table_name
 * @property integer $old_id
 * @property integer $new_id
 */
class TemporaryMergerId extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'temporary_merger_id';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['old_id', 'new_id'], 'integer'],
            [['table_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_name' => 'Table Name',
            'old_id' => 'Old ID',
            'new_id' => 'New ID',
        ];
    }
}
