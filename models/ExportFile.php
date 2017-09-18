<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "export_file".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $item_list
 * @property string $path
 * @property string $file
 * @property integer $created_at
 *
 * @property User $user
 */
class ExportFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'export_file';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['item_list'], 'string'],
            [['path', 'file', 'export_type'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'item_list' => 'Item List',
            'path' => 'Path',
            'file' => 'File',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function findByUserId($userId, $type)
    {
        $query = static::find()
            ->where([
                'user_id' => $userId,
                'export_type' => $type,
            ]);

        return $query->one();
    }

    public static function createInstance($file, $path)
    {
        $type = static::getExportTypeFromFile($file);
        if ($doc = static::findByUserId(Yii::$app->user->id, $type)) {
            $doc->created_at = time();
        } else {
            $doc = new static([
                'file' => $file,
                'path' => $path,
                'export_type' => $type,
            ]);
        }

        return $doc->save();
    }

    public static function getExportTypeFromFile($file)
    {
        $pieces = explode('---', $file, 2);
        $typeWithExtension = trim($pieces[1]);
        $pieces = explode('.', $typeWithExtension);

        return trim($pieces[0]);
    }
}
