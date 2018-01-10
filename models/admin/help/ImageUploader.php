<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 09.01.18
 * Time: 15:29
 */

namespace app\models\admin\help;


use app\helpers\ModelHelper;
use yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class ImageUploader
 * @package app\models\admin\help
 * @property UploadedFile $upload
 */
class ImageUploader extends Model
{
    const MAX_FILE_SIZE = 5242880;
    const DIR_OF_IMAGES = "/uploads/admin/images/";
    public $upload;
    public $ckCsrfToken;
    public $CKEditorFuncNum = 0; //5mb
    public $fileName;

    public function rules()
    {
        return [
            [
                'upload', 'file', 'extensions' => ['jpg', 'jpge', 'png'],
                'maxSize' => self::MAX_FILE_SIZE,
                'tooBig' => 'Максимальный размер файла 5мб',
                'wrongExtension' => 'Допустимые типы файлов jpg, jpge, png',
                'skipOnEmpty' => false
            ],
            ['ckCsrfToken', 'safe']
        ];
    }

    public function upload($CKEditorFuncNum = 0)
    {
        $this->CKEditorFuncNum = $CKEditorFuncNum;
        $this->prepare();
        if ($this->validate()) {
            if (!file_exists($this->getSavePath())) {
                Yii::trace($this->getSavePath());
                mkdir($this->getSavePath(), 0777, true);
            }
            $this->generateAndSetName();

            return $this->upload->saveAs($this->getSavePath() . $this->getName());
        } else {
            return false;
        }
    }

    public function prepare()
    {

        $this->upload = UploadedFile::getInstanceByName('upload');
    }

    public function getSavePath()
    {
        return Yii::getAlias('@pfdoroot') . self::DIR_OF_IMAGES;
    }

    public function generateAndSetName()
    {
        $this->fileName = md5($this->upload->baseName . time())
            . '.' . $this->upload->extension;
    }

    public function getName()
    {
        return $this->fileName;
    }

    public function getResponse()
    {
        $url = ModelHelper::getFirstError($this);
        $js = <<<JS
window.parent.CKEDITOR.tools.callFunction(
    "{$this->CKEditorFuncNum}",
    "{$this->getUrl()}",
    "$url"
    );
JS;
        $response = '<script type="text/javascript">' . $js . '</script>';

        return $response;
    }

    public function getUrl()
    {
        if (!$this->getName()) {
            return '';
        }

        return Yii::getAlias('@pfdo') . self::DIR_OF_IMAGES . $this->getName();
    }
}
