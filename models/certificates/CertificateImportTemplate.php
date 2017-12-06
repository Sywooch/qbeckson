<?php

namespace app\models\certificates;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * шаблон импорта списка сертификатов
 *
 * @property int $id [int(11)]
 * @property string $path [varchar(255)]  путь к шаблону импорта списка сертификатов
 * @property string $base_url [varchar(255)]  ссылка к шаблону импорта списка сертификатов
 */
class CertificateImportTemplate extends ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $certificateImportTemplate;

    /** @inheritdoc */
    public static function tableName()
    {
        return 'certificate_import_template';
    }
    
    /** @inheritdoc */
    public function rules()
    {
        return [
            ['certificateImportTemplate', 'safe'],
            [['path', 'base_url'], 'string'],
        ];
    }
    
    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'path' => 'Путь к шаблону импорта списка сертификатов',
            'certificateImportTemplate' => 'Шаблон импорта списка сертификатов',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => UploadBehavior::class,
                'pathAttribute' => 'path',
                'baseUrlAttribute' => 'base_url',
                'attribute' => 'certificateImportTemplate',
            ]
        ];
    }

    /**
     * ссылка для загрузки шаблона импорта списка сертификатов
     *
     * @param string $fileStorage
     *
     * @return string|null
     */
    public function templateDownloadUrl($fileStorage = 'fileStorage')
    {
        if (!file_exists(Yii::$app->$fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $this->path)) {
            return null;
        }

        return $this->base_url . '/' . $this->path;
    }

    /**
     * существует ли шаблон импорта списка сертификатов
     *
     * @return boolean
     */
    public static function exists()
    {
        return self::find()->exists();
    }
}