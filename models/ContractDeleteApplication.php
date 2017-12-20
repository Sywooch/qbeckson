<?php

namespace app\models;

use app\behaviors\UploadBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Url;

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
 * @property integer $contract_id
 * @property integer $status
 *
 * @property Contracts $contract
 */
class ContractDeleteApplication extends ActiveRecord
{
    const STATUS_WAITING = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_REFUSED = 3;

    /** Сценарий для подачи заявки на удаление договора */
    const SCENARIO_CREATE = 'create';

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
            [['isChecked'], 'required', 'requiredValue' => 1, 'message' => 'Подтвердите ознакомление с условиями направления запроса на удаление договора'],
            [['created_at', 'confirmed_at'], 'safe'],
            [['contract_id', 'status'], 'integer'],
            [['status'], 'in', 'range' => [self::STATUS_WAITING, self::STATUS_CONFIRMED, self::STATUS_REFUSED]],
            [['status'], 'default', 'value' => self::STATUS_WAITING],
            [['reason', 'file', 'base_url', 'filename'], 'string', 'max' => 255],
            [['confirmationFile'], 'required', 'on' => self::SCENARIO_CREATE],
            [['contract_id'], 'unique', 'message' => 'Запрос на удаление этого договора уже отправлен.'],
            [
                ['contract_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Contracts::className(),
                'targetAttribute' => ['contract_id' => 'id']
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
     * @return null|string
     */
    public function getFileUrl()
    {
        return $this->base_url ? Url::to([$this->base_url . DIRECTORY_SEPARATOR . $this->file], true) : null;
    }
}
