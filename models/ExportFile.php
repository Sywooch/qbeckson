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
 * @property string $data_provider
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
                'attributes1' => ['data_provider', 'columns'],
                'attributes2' => ['data_provider', 'columns'],
                'useClosureSerializator' => true,
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
            [['created_at', 'data_provider', 'columns'], 'safe'],
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

    public static function createInstance($file, $group, $table, $dataProvider, $columns)
    {
        if ($doc = static::findByUserId(Yii::$app->user->id, $group)) {
            $doc->created_at = time();
            $doc->status = static::STATUS_PROCESS;
            $doc->data_provider = $dataProvider;
            $doc->columns = $columns;
        } else {
            $doc = new static([
                'file' => $file,
                'export_type' => $group,
                'group' => $group,
                'columns' => $columns,
                'data_provider' => $dataProvider,
                'table' => $table,
            ]);
        }

        if ($doc->save()) {
            return $doc;
        }

        return null;
    }

    public function setReady() {
        $this->status = self::STATUS_READY;

        return $this->save(false, ['status']);
    }

    public function getCannotBeUpdated() {
        $coefficient = 5;
        if (YII_ENV_DEV) {
            $coefficient = 500;
        }

        $newTime = $this->created_at + $this->data_provider->totalCount / $coefficient;

        if ($newTime > time()) {
            return $newTime;
        }

        return false;
    }
}
