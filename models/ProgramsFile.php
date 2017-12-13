<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class ProgramsFile extends Model
{
    /**
     * @var UploadedFile
     */
    public $docFile;
    public $newprogram;

    public function rules()
    {
        return [
            [
                ['docFile'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'doc, docx, pdf', 'maxSize' => 1024 * 1024 * 20
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'docFile' => 'Образовательная программа',
            'newprogram' => 'Изменить программу',
        ];
    }

    public function upload($filename)
    {
        if ($this->validate()) {
            $this->docFile->saveAs(\Yii::getAlias('@pfdoroot/uploads/programs/') . $filename);

            return true;
        } else {
            return false;
        }
    }
}
