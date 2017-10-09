<?php

namespace app\models;

use app\behaviors\ArrayOrStringBehavior;
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
 * $property integer $status
 * @property string $search_model
 * @property string $columns
 * @property string $group
 * @property string $table
 *
 * @property User $user
 */
class ExportFile extends \yii\db\ActiveRecord
{
    const STATUS_PROCESS = 0;
    const STATUS_READY = 10;

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
            'array2string' => [
                'class' => ArrayOrStringBehavior::className(),
                'attributes1' => ['search_model', 'columns'],
                'attributes2' => ['search_model', 'columns'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status'], 'integer'],
            [['item_list', 'group', 'table'], 'string'],
            [['path', 'file', 'export_type'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['created_at', 'search_model', 'columns'], 'safe'],
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

    public static function findByUserId($userId, $type, $status = null)
    {
        $query = static::find()
            ->where([
                'user_id' => $userId,
                'export_type' => $type,
            ]);

        $query->andFilterWhere(['status' => $status]);

        return $query->one();
    }

    public static function createInstance($file, $group, $table, $searchModel, $columns)
    {
        if ($doc = static::findByUserId(Yii::$app->user->id, $group)) {
            $doc->created_at = time();
        } else {
            $doc = new static([
                'file' => $file,
                'export_type' => $group,
                'group' => $group,
                'columns' => $columns,
                'search_model' => $searchModel,
                'table' => $table,
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
