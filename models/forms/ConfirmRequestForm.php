<?php

namespace app\models\forms;

use app\models\Cooperate;
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
    private $model;

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
                    return (int)$model->type === Cooperate::DOCUMENT_TYPE_EXTEND;
                }
            ],
            [
                'document', 'required', 'when' => function ($model) {
                    /** @var self $model */
                    return (int)$model->type === Cooperate::DOCUMENT_TYPE_CUSTOM;
                }
            ],
            ['value', 'in', 'range' => array_keys(Cooperate::documentTypes())],
            ['document', 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Тип документа',
            'document' => 'Документ',
            'value' => 'Укажите сумму средств по договору на ' . date('Y') . ' год'
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (null !== ($model = $this->getModel()) && $this->validate()) {
            $model->document_type = $this->type;
            if ((int)$this->type === Cooperate::DOCUMENT_TYPE_EXTEND) {
                //TODO гененируем docx файл
            }
            if ((int)$this->type === Cooperate::DOCUMENT_TYPE_CUSTOM) {
                $model->document = $this->document;
            }
            $model->confirm();

            return $model->save();
        }

        return false;
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
