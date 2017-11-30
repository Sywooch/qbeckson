<?php

namespace app\components\periodicField;

use app\models\ProgrammeModule;
use app\models\Programs;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class PeriodicFieldAR
 * @package app\components\periodicField
 *
 * @property string $table_name
 * @property string $field_name
 * @property int $record_id
 * @property int $created_at
 * @property int $created_by
 * @property string $value
 *
 * @property string $fieldLabel
 *
 * @property ActiveRecord $model
 *
 */
class PeriodicFieldAR extends \yii\db\ActiveRecord
{
    private $model;

    private $tablesToModels = [
        'programs' => Programs::class,
        'years' => ProgrammeModule::class
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%periodic_field}}';
    }

    public function init()
    {
        parent::init();
        $this->on(
            'afterFind',
            function () {
                $this->model = $this->tablesToModels[$this->table_name]::instantiate(null);
            }
        );
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => null,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['table_name', 'field_name', 'record_id'], 'required'],
            [['record_id'], 'integer'],
            [['value'], 'string'],
            [['table_name', 'field_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_name' => 'Таблица',
            'field_name' => 'Поле',
            'fieldLabel' => 'Поле',
            'record_id' => 'Id записи',
            'created_at' => 'Дата',
            'created_by' => 'Пользователь',
            'value' => 'Значение',
            'resolvedValue' => 'Значение',
        ];
    }

    public function getFieldLabel()
    {
        return $this->model->attributeLabels()[$this->field_name];
    }

    public function getResolvedValue()
    {
        return $this->model->fieldResolver($this);
    }
}
