<?php

namespace app\models;

use app\behaviors\UploadBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "contract_delete_application".
 *
 * @property integer $id
 * @property string $reason
 * @property string $file
 * @property string $base_url
 * @property string $filename
 * @property string $created_at
 * @property string $confirmed_at
 * @property string $contract_date
 * @property string $contract_number
 * @property integer $contract_id
 * @property string $certificate_number
 * @property integer $status
 * @property integer $organization_id
 *
 * @property Contracts $contract
 * @property Organization $organization
 */
class ContractDeleteApplication extends ActiveRecord
{
    const STATUS_WAITING = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_REFUSED = 3;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_CONFIRM = 'confirm';
    const SCENARIO_REJECT = 'reject';

    const FILE_UPLOAD_PATH = '/uploads/contract-delete-application';

    public $confirmationFile;
    public $isChecked;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contract_delete_application';
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors()
    {
        return [
            'uploadConfirmationFile' => [
                'class' => UploadBehavior::className(),
                'attribute' => 'confirmationFile',
                'pathAttribute' => 'file',
                'baseUrlAttribute' => 'base_url',
                'nameAttribute' => 'filename',
            ],
            'TimestampBehavior' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reason'], 'required'],
            [
                ['isChecked'],
                'required',
                'requiredValue' => 1,
                'except' => [self::SCENARIO_CONFIRM, self::SCENARIO_REJECT],
                'message' => 'Подтвердите ознакомление с условиями направления запроса на удаление договора'
            ],
            [['created_at', 'confirmed_at'], 'safe'],
            [['contract_date'], 'date', 'format' => 'php:Y-m-d'],
            [['contract_id', 'status', 'organization_id'], 'integer'],
            [['status'], 'in', 'range' => [self::STATUS_WAITING, self::STATUS_CONFIRMED, self::STATUS_REFUSED]],
            [['status'], 'default', 'value' => self::STATUS_WAITING],
            [['reason', 'file', 'base_url', 'filename'], 'string', 'max' => 255],
            [['contract_number'], 'string', 'max' => 11],
            [['certificate_number'], 'string', 'max' => 45],
            [['confirmationFile'], 'required', 'on' => self::SCENARIO_CREATE],
            [
                ['contract_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Contracts::className(),
                'targetAttribute' => ['contract_id' => 'id'],
                'except' => self::SCENARIO_CONFIRM
            ],
            [
                ['organization_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Organization::className(),
                'targetAttribute' => ['organization_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reason' => 'Основание',
            'filename' => 'Filename',
            'contract_id' => 'Договор',
            'created_at' => 'Дата подачи заявки',
            'confirmed_at' => 'Дата решения',
            'status' => 'Статус',
            'fileUrl' => 'Подтверждающий документ',
            'confirmationFile' => 'Подтверждающий документ',
            'contract_number' => 'Номер договора',
            'contract_date' => 'Дата договора',
            'certificate_number' => 'Номер сертификата',
            'isChecked' => 'Мы ознакомлены с условиями направления запроса на удаление договора. Подтверждаем, что наш запрос удовлетворяет всем четырем условиям. Уверены, что в нашем случае негативных последствий не возникнет',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'confirmationFile' => 'Приложите сопроводительное письмо для удаления договора, подписанное руководителем организации (отсканированное)',
            'isChecked' => 'Доступно после добавления подтверждающего документа',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Contracts::className(), ['id' => 'contract_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'organization_id']);
    }

    /**
     * @return null|string
     */
    public function getFileUrl()
    {
        return $this->file ? \Yii::getAlias('@pfdo') . $this::FILE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $this->file : null;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteContractConfirm()
    {
        $contract = $this->contract;
        if ($contract) {
            $this->status = self::STATUS_CONFIRMED;
            $this->confirmed_at = new Expression('NOW()');
            $this->contract_date = $contract->date;
            $this->contract_number = $contract->number;
            $this->contract_id = null;
            $this->certificate_number = ArrayHelper::getValue($contract, ['certificate', 'number']);
            $this->setScenario(self::SCENARIO_CONFIRM);
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if ($contract->refoundMoney() && $contract->delete() !== false && $this->save()) {
                    $transaction->commit();
                    return true;
                } else {
                    $transaction->rollBack();
                    return false;
                }
            } catch (Exception $exception) {
                if ($transaction->isActive) {
                    $transaction->rollBack();
                }
                throw $exception;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function deleteContractReject()
    {
        $this->status = self::STATUS_REFUSED;
        $this->confirmed_at = new Expression('NOW()');
        $this->setScenario(self::SCENARIO_REJECT);
        if ($this->save(false)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        //Переносим файл в другое место
        if ($insert || isset($changedAttributes['file']) || isset($changedAttributes['base_url'])) {
            $parts = explode('.', $this->file);
            $ext = $parts[count($parts) - 1];
            $filename = 'delete-' . $this->contract_id . '-' . $this->id . '.' . $ext;
            $file_path = \Yii::getAlias('@pfdoroot') . self::FILE_UPLOAD_PATH;
            $oldFile = \Yii::getAlias('@pfdoroot/uploads') . DIRECTORY_SEPARATOR . $this->file;
            $newFile = $file_path . DIRECTORY_SEPARATOR . $filename;

            if (file_exists($oldFile)) {
                if (!file_exists($file_path)) {
                    FileHelper::createDirectory($file_path);
                }
                if (rename($oldFile, $newFile)) {
                    //updateAll не запустит повторно событие afterSave
                    static::updateAll(['file' => $filename, 'base_url' => self::FILE_UPLOAD_PATH], ['id' => $this->id]);
                }
            }

        }
    }
}
