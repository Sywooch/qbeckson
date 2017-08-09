<?php

namespace app\models\forms;

use app\models\Cooperate;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
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
    private $phpWord;

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
            'value' => 'Укажите сумму средств по договору на ' . date('Y') . ' год',
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
            if ((int)$this->type === Cooperate::DOCUMENT_TYPE_CUSTOM) {
                $model->document = $this->document;
            } else {
                if ((int)$this->type === Cooperate::DOCUMENT_TYPE_EXTEND) {

                } else {

                }
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

    /**
     * @return PhpWord
     */
    public function getPhpWord()
    {
        if (null === $this->phpWord) {
            $this->phpWord = new PhpWord();
        }

        return $this->phpWord;
    }




    /*if ((int)$this->type === Cooperate::DOCUMENT_TYPE_EXTEND) {
        $phpWord = $this->generateExtendDocx();
    } else {
        $phpWord = $this->generateGeneralDocx();
    }
    $objWriter = IOFactory::createWriter($phpWord);
    $objWriter->save(Yii::getAlias('@runtime/new.docx'));*/
    /**
     * @return PhpWord
     * @deprecated
     */
    /*private function generateGeneralDocx()
    {
        $phpWord = $this->getPhpWord();
        $section = $phpWord->addSection();
        $section->addText(
            '"Learn from yesterday, live for today, hope for tomorrow. '
            . 'The important thing is not to stop questioning." '
            . '(Albert Einstein)'
        );

        return $phpWord;
    }*/
    /**
     * @return PhpWord
     * @deprecated
     */
    /*private function generateExtendDocx()
    {
        $phpWord = $this->getPhpWord();
        $section = $phpWord->addSection();
        $section->addText(
            '"Learn from yesterday, live for today, hope for tomorrow. '
            . 'The important thing is not to stop questioning." '
            . '(Albert Einstein)'
        );
        $section->addText(
            '"Great achievement is usually born of great sacrifice, '
            . 'and is never the result of selfishness." '
            . '(Napoleon Hill)',
            ['name' => 'Tahoma', 'size' => 10]
        );

        return $phpWord;
    }*/
}
