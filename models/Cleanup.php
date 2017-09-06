<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\forms\ContractRemoveForm;

class Cleanup extends Model
{
    /**
     * @var UploadedFile
     */
    public $importFile;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['importFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'csv'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'importFile' => 'Файл с данными',
        ];
    }

    public function removeChildrenFromCsv()
    {
        if (($handle = fopen($this->importFile->tempName, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                $username = $data[0];
                $user = User::find()
                    ->where(['username' => $username])
                    ->one();

                if ($user) {
                    $user->delete();
                }
            }
        }

        return true;
    }
}
