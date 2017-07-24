<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class Import extends Model
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

    public function insertChildrenFromCsv()
    {
        if (($handle = fopen($this->importFile->tempName, "r")) !== false) {
            $i = 0;
            $certificateArray = [];
            $userArray = [];
            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                $username = $data[3];
                // Пропускаем первый ряд или ряды без username
                if (!$i++ || intval($username) < 1) {
                    continue;
                }
                $user = null;
                // Пропускаем, если есть такой юзер с сертификатом
                if ($userCount = User::find()->where(['username' => $username])->count()) {
                    $certificateCount = Certificates::find()->where(['number' => $username])->count();
                    if (!$certificateCount && !in_array($username, $userArray)) {
                        $user = User::find()->where(['username' => $username])->one();
                        array_push($userArray, $username);
                    } else {
                        die('Такой логин как ' . $username . ' уже существует');
                    }
                }
                if (empty($user)) {
                    Yii::$app->db->createCommand()->insert('user', [
                        'username' => $username,
                        'password' => Yii::$app->security->generatePasswordHash($data[22], 7),
                    ])->execute();
                    $userId = Yii::$app->db->lastInsertID;
                    Yii::$app->db->createCommand()->insert('auth_assignment', [
                        'item_name' => 'certificate',
                        'user_id' => $userId,
                        'created_at' => time(),
                    ])->execute();
                } else {
                    $userId = $user->id;
                }
                $certificateArray[] = [$userId, $username, $data[4], $data[5], $this->convertEncoding($data[6]), $this->convertEncoding($data[7]), $this->convertEncoding($data[8]), $this->convertEncoding($data[9]), $data[11], $data[12], $data[20], $data[21]];
                //Yii::$app->db->createCommand()->batchInsert('certificates', ['user_id', 'number', 'payer_id', 'actual', 'fio_child', 'name', 'soname', 'phname', 'nominal', 'balance', 'cert_group', 'rezerv'], $certificateArray)->execute();
                //$certificateArray = [];
            }
            fclose($handle);
            Yii::$app->db->createCommand()->batchInsert('certificates', ['user_id', 'number', 'payer_id', 'actual', 'fio_child', 'name', 'soname', 'phname', 'nominal', 'balance', 'cert_group', 'rezerv'], $certificateArray)->execute();
        }

        return true;
    }

    private function convertEncoding($text, $in = "CP1251", $out = "UTF-8")
    {
        return iconv($in, $out, $text);
    }
}
