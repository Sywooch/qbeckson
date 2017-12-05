<?php

namespace app\models\certificates;

use app\models\Certificates;
use app\models\Payers;
use app\models\User;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * реестр импорта сертификатов и пользователей
 *
 * @property int $id [int(11)]
 * @property int $payer_id [int(11)]  id плательщика импортирующего список сертификатов
 * @property string $certificate_list_for_import_path [varchar(255)]  путь до файла со списком импортируемых сертификатов
 * @property string $certificate_list_for_import_base_url [varchar(255)]  ссылка для файла со списком импортируемых сертификатов
 * @property string $registry_path [varchar(255)]  путь до файла с реестром импортированных сертификатов и пользователей
 * @property string $registry_base_url [varchar(255)]  ссылка для файла с реестром импортированных сертификатов и пользователей
 * @property bool $is_registry_downloaded [tinyint(1)]  был ли скачан реестр после импорта списка сертификатов и пользователей
 * @property string $registry_created_at [datetime]  дата и время создания файла реестра
 *
 * @property null|string $certificateImportFilePath
 * @property void $registryUrl
 */
class CertificateImportRegistry extends ActiveRecord
{
    /**
     * файл для импорта списка сертификатов
     *
     * @var array|string
     */
    public $certificateImportListFile;

    /**
     * файл реестра импортированных сертификатов и пользователей
     *
     * @var array|string
     */
    public $certificateListRegistryFile;

    /**
     * список сертификатов для импорта
     *
     * @var array
     */
    public $certificateImportList;

    /** @inheritdoc */
    public static function tableName()
    {
        return 'certificate_import_registry';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            ['payer_id', 'exist', 'targetClass' => Payers::className(), 'targetAttribute' => 'id'],
            [['certificate_list_for_import_path', 'certificate_list_for_import_base_url', 'registry_path', 'registry_base_url'], 'string', 'max' => 255],
            [['certificateImportListFile', 'certificateListRegistryFile'], 'safe'],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'certificateImportListFile' => 'Импортировать список сертификатов',
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
                'attribute' => 'certificateImportListFile',
                'pathAttribute' => 'certificate_list_for_import_path',
                'baseUrlAttribute' => 'certificate_list_for_import_base_url',
            ],
            [
                'class' => UploadBehavior::class,
                'attribute' => 'certificateListRegistryFile',
                'pathAttribute' => 'registry_path',
                'baseUrlAttribute' => 'registry_base_url',
            ],
        ];
    }

    /**
     * проверить формат загруженного файла xlsx со списком сертификатов
     *
     * @return boolean
     */
    public function checkFileFormat()
    {
        try {
            libxml_disable_entity_loader(false);
            $reader = ReaderFactory::create(Type::XLSX);
            $reader->open($this->getCertificateImportFilePath());

            foreach ($reader->getSheetIterator() as $sheet) {
                $rowNumber = 1;
                foreach ($sheet->getRowIterator() as $row) {
                    if ($rowNumber == 1 && 'ZagrSp1' != $row[9] || $rowNumber == 2 && 1 != $row[9]) {
                        $this->addError('certificateListForImport', 'Загружаемый список не соответствует требованиям. Скачайте "таблицу для заполнения" и заполните именно ее');

                        return false;
                    }

                    $rowNumber++;
                }

                break;
            }

            $reader->close();
        } catch (IOException $e) {
            $this->addError('certificateListForImport', 'Произошла ошибка при открытии файла');

            return false;
        }

        return true;
    }

    /**
     * создать список сертификатов из загруженного файла
     */
    public function importCertificateList()
    {
        set_time_limit(1800);

        $this->deleteRegistry();
        $this->is_registry_downloaded = 0;
        $this->loadCertificatesAndUsersFromFile();
        $this->deleteCertificateListForImport();
        $this->saveLoadedCertificatesAndUsers();
        $this->createRegistryForCurrentPayer();

        return true;
    }

    /**
     * загрузить импортируемый список сертификатов из файла
     */
    private function loadCertificatesAndUsersFromFile() {
        /** @var $payer Payers */
        $payer = Yii::$app->user->identity->payer;
        $region = Yii::$app->operator->identity->region;

        $usernameList = User::find()->select('username')->asArray()->all();

        $certificateImportList = [];

        try {
            libxml_disable_entity_loader(false);
            $reader = ReaderFactory::create(Type::XLSX);
            $reader->open($this->getCertificateImportFilePath());

            foreach ($reader->getSheetIterator() as $sheet) {
                $rowNumber = 1;
                foreach ($sheet->getRowIterator() as $row) {
                    if ($rowNumber >= 15 && $rowNumber < 5015) {
                        if ('-' == $row[2]) {
                            break;
                        }

                        do {
                            $username = $region . $payer->code . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                        } while (in_array($username, $usernameList) || in_array($username, ArrayHelper::map($certificateImportList, 'username', 'username')));

                        $certificateImportList[] = [
                            'payer_id' => $payer->id,
                            'username' =>  $username,
                            'last_name' => $row[3],
                            'first_name' => $row[4],
                            'patronymic' => $row[5],
                        ];
                    }

                    $rowNumber++;
                }

                break;
            }

            $reader->close();
        } catch (IOException $e) {
            return false;
        }

        $this->certificateImportList = $certificateImportList;

        return true;
    }

    /**
     * импортировать сертификаты и пользователей для сертификатов, если их нет, иначе изменить пароль пользователю
     */
    private function saveLoadedCertificatesAndUsers()
    {
        $transaction = Yii::$app->db->beginTransaction();

        $userNameList = ArrayHelper::map(User::find()->select('username')->asArray()->all(), 'username', 'username');
        $certificateNumberList = ArrayHelper::map(Certificates::find()->select('number')->asArray()->all(), 'number', 'number');

        $certificateImportBufferUsernameList = ArrayHelper::map($this->certificateImportList, 'username', 'username');

        /** @var Payers $payer */
        $payer = Yii::$app->user->identity->payer;
        $userMunId = $payer->mun;

        $newUserList = [];
        $userExistList = [];
        $userExistListCondition = [];

        $certificateList = [];

        foreach ($this->certificateImportList as &$certificateImportBuffer) {
            $password = Yii::$app->getSecurity()->generateRandomString($length = 10);
            $passwordHash = Yii::$app->getSecurity()->generatePasswordHash($password);

            $certificateImportBuffer['password'] = $password;

            if (!in_array($certificateImportBuffer['username'], $userNameList)) {
                $newUserList[] = ['username' => $certificateImportBuffer['username'], 'password' => $passwordHash, 'access_token' => '', 'auth_key' => '', 'mun_id' => $userMunId];
            } else {
                $userExistList = ['password' => $passwordHash];
                $userExistListCondition = ['username' => $certificateImportBuffer['username']];
            }
        }

        $userColumns = ['username', 'password', 'access_token', 'auth_key', 'mun_id'];

        $insertCount = 0;
        if (count($newUserList) > 0) {
            $insertCount = Yii::$app->db->createCommand()->batchInsert(User::tableName(), $userColumns, $newUserList)->execute();
        }

        if (count($userExistList) > 0) {
            Yii::$app->db->createCommand()->update(User::tableName(), $userExistList, $userExistListCondition)->execute();
        }

        $userIdList = ArrayHelper::map(User::find()->select('username, id')->where(['username' => $certificateImportBufferUsernameList])->asArray()->all(), 'username', 'id');

        ArrayHelper::map(User::find()->select('username, id')->where(['username' => ArrayHelper::map($newUserList, 'username', 'username')])->asArray()->all(), 'username', 'id');

        $userIdListForLog = [];
        foreach ($userIdList as $userId) {
            $userIdListForLog[] = ['user_id' => $userId, 'created_at' => date('Y-m-d H:i:s')];
        }

        if ($insertCount > 0) {
            Yii::$app->db->createCommand()->batchInsert(UserCreatedOnCertificateImportLog::tableName(), ['user_id', 'created_at'], $userIdListForLog)->execute();
        }

        $possibleCertGroupId = $payer->getCertGroups()->one()->id;
        $certGroupId = $payer->getCertGroups(1)->one()->id;

        foreach ($this->certificateImportList as $certificateImport) {
            if (!in_array($certificateImport['username'], $certificateNumberList)) {
                $certificateList[] = [
                    'user_id' => $userIdList[$certificateImport['username']],
                    'payer_id' => $payer->id,
                    'number' => $certificateImport['username'],
                    'actual' => 1,
                    'rezerv_f' => 0,
                    'rezerv' => 0,
                    'soname' => $certificateImport['last_name'],
                    'name' => $certificateImport['first_name'],
                    'phname' => $certificateImport['patronymic'],
                    'fio_child' => $certificateImport['last_name'] . ' ' . $certificateImport['first_name'] . ' ' . $certificateImport['patronymic'],
                    'possible_cert_group' => $possibleCertGroupId,
                    'updated_cert_group' => 0,
                    'cert_group' => $certGroupId,
                    'nominal' => 0,
                    'nominal_f' => 0,
                    'balance' => 0,
                    'balance_f' => 0,
                    'created_at' => date('Y-m-d H-i-s'),
                ];
            }
        }

        $certificatesColumns = ['user_id', 'payer_id', 'number', 'actual', 'rezerv_f', 'rezerv', 'soname', 'name', 'phname', 'fio_child', 'possible_cert_group', 'updated_cert_group', 'cert_group', 'nominal', 'nominal_f', 'balance', 'balance_f', 'created_at'];

        Yii::$app->db->createCommand()->batchInsert(Certificates::tableName(), $certificatesColumns, $certificateList)->execute();

        $transaction->commit();
    }

    /**
     * импортировать и сохранить список сертификатов
     */
    private function createRegistryForCurrentPayer()
    {
        $filePath = 'certificate-registry-' . Yii::$app->user->identity->payer->id . '.xlsx';

        $this->certificateListRegistryFile = [
            'path' => $filePath,
            'base_url' => '/file/registries?path='];

        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToFile(Yii::$app->fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $filePath);
        $writer->addRow(['№', 'Номер сертификата', 'Фамилия', 'Имя', 'Отчество', 'Тип сертификата', 'Пароль']);

        $i = 1;
        foreach ($this->certificateImportList as $createdCertificateAndUser) {
            $writer->addRow([$i++, $createdCertificateAndUser['username'], $createdCertificateAndUser['last_name'], $createdCertificateAndUser['first_name'], $createdCertificateAndUser['patronymic'], 'Сертификат учета', $createdCertificateAndUser['password']]);
        }

        $writer->close();

        $this->registry_created_at = date('Y-m-d H-i-s');
    }

    /**
     * получить путь к загруженному файлу импорта сертификатов и пользователей
     */
    private function getCertificateImportFilePath()
    {
        if (!key_exists('path', $this->certificateImportListFile) && !file_exists(Yii::$app->fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $this->certificateImportListFile['path'])) {
            return null;
        }

        return Yii::$app->fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $this->certificateImportListFile['path'];
    }

    /**
     * получить ссылку к файлу реестра импортированных сертификатов и пользователей
     */
    public function getRegistryUrl()
    {
        if (!key_exists('path', $this->certificateImportListFile) && !file_exists(Yii::$app->fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $this->certificateListRegistryFile['path'])) {
            return null;
        }

        return Yii::$app->fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $this->certificateListRegistryFile['path'];
    }

    /**
     * существует ли реестр импортированных сертификатов и пользователей
     */
    public function registryFileExist()
    {
        return $this->registry_path && file_exists(Yii::$app->fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $this->registry_path);
    }

    /**
     * удалить файл для импорта списка сертификатов
     */
    private function deleteCertificateListForImport()
    {
        $filePath = Yii::$app->fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $this->certificate_list_for_import_path;
        if ($this->certificate_list_for_import_path && file_exists($filePath) && unlink($filePath)) {
            $this->certificateImportListFile = null;
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * удалить реестр импортированных сертификатов и пользователей для текущего плательщика
     */
    public function deleteRegistry()
    {
        $filePath = Yii::$app->fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $this->registry_path;
        if ($this->registry_path && file_exists($filePath) && unlink($filePath)) {
            $this->certificateListRegistryFile = null;

            $this->save();

            return true;
        }

        return false;
    }
}