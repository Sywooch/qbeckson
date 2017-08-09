<?php

namespace app\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "key_storage_item".
 *
 * @property integer $id
 * @property integer $operator_id
 * @property integer $key
 * @property integer $value
 * @property string $comment
 * @property string $type
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Operators $operator
 */
class KeyStorageItem extends ActiveRecord
{
    const TYPE_STRING = 'string';
    const TYPE_FILE = 'file';
    const TYPE_JSON = 'json';

    public $file;

    /**
     * @return array
     */
    public static function types()
    {
        return [
            self::TYPE_STRING => 'Текст',
            self::TYPE_FILE => 'Файл',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%key_storage_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'type'], 'required'],
            ['value', 'required', 'when' => function ($model) {
                /** @var self $model */
                return $model->type === self::TYPE_STRING;
            }],
            ['file', 'required', 'when' => function ($model) {
                /** @var self $model */
                return $model->type === self::TYPE_FILE;
            }],
            [['key', 'type'], 'string', 'max' => 128],
            [['value', 'comment', 'file'], 'safe'],
            [['key'], 'unique'],
            [['operator_id'], 'integer'],
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->type === self::TYPE_FILE) {
                $this->value = !empty($this->file['base_url']) ?
                    $this->file['base_url'] . '/' . $this->file['path'] : $this->file['path'];
            }

            return true;
        }

        return false;
    }

    /**
     * Кастыльнём
     */
    public function afterFind()
    {
        if ($this->type === self::TYPE_FILE) {
            $this->file['base_url'] = '';
            $this->file['path'] = $this->value;
            $this->file['type'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        }
    }

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'operator_id'
                ],
                'value' => Yii::$app->user->getIdentity()->operator->id,
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperator()
    {
        return $this->hasOne(Operators::class, ['id' => 'operator_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Тип',
            'key' => 'Ключ',
            'value' => 'Значение',
            'comment' => 'Название',
            'file' => 'Загрузите файл',
        ];
    }
}
