<?php

namespace app\models\forms;

use app\models\Cooperate;
use app\models\OperatorSettings;
use Yii;
use yii\base\Model;

/**
 * Class ConfirmRequestForm
 * @package app\models\forms
 */
class ConfirmRequestForm extends Model
{
    public $type;
    public $value;
    public $document;
    public $isCustomValue;

    private $model;
    private $operatorSettings;

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [
                'value', 'required', 'when' => function ($model) {
                    /** @var self $model */
                    return $model->type === Cooperate::DOCUMENT_TYPE_EXTEND;
                }
            ],
            [
                'value', 'required', 'when' => function ($model) {
                    /** @var self $model */
                    return (int)$model->isCustomValue === 1;
                }
            ],
            [
                'document', 'required', 'when' => function ($model) {
                    /** @var self $model */
                    return $model->type === Cooperate::DOCUMENT_TYPE_CUSTOM;
                }
            ],
            ['type', 'in', 'range' => array_keys(Cooperate::documentTypes())],
            [['value'], 'integer'],
            [['document', 'isCustomValue'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Тип документа (генерируется в формате Word 2007)',
            'document' => 'Документ',
            'value' => 'Укажите сумму средств по договору',
            'isCustomValue' => 'В договоре устанавливается максимальная сумма',
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (null !== ($model = $this->getModel()) && $this->validate()) {
            $model->document_type = $this->type;
            if ($this->type === Cooperate::DOCUMENT_TYPE_CUSTOM) {
                $model->document = $this->document;
            } else {
                if ($this->type === Cooperate::DOCUMENT_TYPE_GENERAL) {
                    $model->document = [
                        'path' => $this->getOperatorSettings()->general_document_path,
                        'base_url' => $this->getOperatorSettings()->general_document_base_url,
                    ];
                }
                if ($this->type === Cooperate::DOCUMENT_TYPE_EXTEND) {
                    $model->document = [
                        'path' => $this->getOperatorSettings()->extend_document_path,
                        'base_url' => $this->getOperatorSettings()->extend_document_base_url,
                    ];
                }
            }
            $model->total_payment_limit = $this->value;
            $model->confirm();

            return $model->save();
        }

        return false;
    }

    /**
     * @return OperatorSettings
     */
    public function getOperatorSettings()
    {
        if (null === $this->operatorSettings) {
            $this->operatorSettings = Yii::$app->operator->identity->settings;
        }
        return $this->operatorSettings;
    }

    /**
     * @return Cooperate
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Cooperate $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }
}
