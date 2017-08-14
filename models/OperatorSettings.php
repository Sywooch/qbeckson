<?php

namespace app\models;

use trntv\filekit\behaviors\UploadBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "operator_settings".
 *
 * @property integer $id
 * @property integer $operator_id
 * @property string $general_document_path
 * @property string $general_document_base_url
 * @property string $document_name
 * @property string $extend_document_path
 * @property string $extend_document_base_url
 * @property integer $current_program_date_from
 * @property integer $current_program_date_to
 * @property integer $future_program_date_from
 * @property integer $future_program_date_to
 *
 * @property string $extendDocumentUrl
 * @property string $generalDocumentUrl
 */
class OperatorSettings extends ActiveRecord
{
    public $extendDocument;
    public $generalDocument;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operator_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'operator_id', 'document_name', 'current_program_date_from', 'current_program_date_to',
                'future_program_date_from', 'future_program_date_to', 'extendDocument', 'generalDocument'
            ], 'required'],
            [['operator_id'], 'integer'],
            [[
                'general_document_path', 'general_document_base_url', 'document_name', 'extend_document_path',
                'extend_document_base_url'
            ], 'string', 'max' => 255],
            [[
                'extendDocument', 'generalDocument', 'current_program_date_from', 'current_program_date_to',
                'future_program_date_from', 'future_program_date_to'
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => UploadBehavior::class,
                'attribute' => 'generalDocument',
                'pathAttribute' => 'general_document_path',
                'baseUrlAttribute' => 'general_document_base_url',
            ],
            [
                'class' => UploadBehavior::class,
                'attribute' => 'extendDocument',
                'pathAttribute' => 'extend_document_path',
                'baseUrlAttribute' => 'extend_document_base_url',
            ],
        ];
    }

    /**
     * @return string
     */
    public function getGeneralDocumentUrl()
    {
        return null !== $this->general_document_base_url ?
            $this->general_document_base_url . '/' . $this->general_document_path : null;
    }

    /**
     * @return string
     */
    public function getExtendDocumentUrl()
    {
        return null !== $this->extend_document_base_url ?
            $this->extend_document_base_url . '/' . $this->extend_document_path : null;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'operator_id' => 'Operator ID',
            'document_name' => 'Название документа',

            'current_program_date_from' => 'Дата с',
            'current_program_date_to' => 'Дата до',
            'future_program_date_from' => 'Дата с',
            'future_program_date_to' => 'Дата до',

            'extendDocument' => 'Типовой договор с суммой',
            'generalDocument' => 'Типовой договор без суммы',
        ];
    }
}