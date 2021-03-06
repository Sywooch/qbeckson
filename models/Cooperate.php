<?php

namespace app\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "cooperate".
 *
 * @property integer $id
 * @property integer $organization_id
 * @property integer $payer_id
 * @property string $date
 * @property string $created_date
 * @property string $date_dissolution
 * @property integer $status
 * @property string $reade
 * @property string $number
 * @property string $reject_reason
 * @property string $appeal_reason
 * @property string $document_type
 * @property string $document_path
 * @property string $document_base_url
 * @property float current_year_payment
 * @property float total_payment
 * @property float current_year_payment_limit
 * @property float total_payment_limit
 * @property string additional_number
 * @property string additional_date
 * @property string additional_document_base_url
 * @property string additional_document_path
 * @property bool $period [tinyint(1)] период действия соглашения, 0 - архивный, 1 - текущий, 2 - будущий
 *
 * @property Organization $organization
 * @property Payers $payers
 * @property null|string $activeDocumentUrl
 * @property null|string $alternativeDocumentUrl
 * @property Payers $payer
 * @property Contracts[] $contracts
 */
class Cooperate extends ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_REJECTED = 2;
    const STATUS_CONFIRMED = 3;
    const STATUS_APPEALED = 4;

    const SCENARIO_REJECT = 'reject';
    const SCENARIO_APPEAL = 'appeal';
    const SCENARIO_REQUISITES = 'requisites';
    const SCENARIO_LIMIT = 'limit';

    const DOCUMENT_TYPE_GENERAL = 'general';
    const DOCUMENT_TYPE_EXTEND = 'extend';
    const DOCUMENT_TYPE_CUSTOM = 'custom';

    const DOCUMENT_NAME_FIRST = 'cooperateFirstDocumentName';
    const DOCUMENT_NAME_SECOND = 'cooperateSecondDocumentName';

    const PERIOD_ARCHIVE = 0;
    const PERIOD_CURRENT = 1;
    const PERIOD_FUTURE = 2;

    public $document;
    public $additionalDocument;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cooperate';
    }

    /**
     * Касталь, никогда так не делать.
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_REJECT] = $scenarios[self::SCENARIO_APPEAL] = $scenarios[self::SCENARIO_REQUISITES] = $scenarios[self::SCENARIO_DEFAULT];
        $scenarios[self::SCENARIO_LIMIT] = ['total_payment_limit'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['organization_id', 'payer_id', 'status', 'created_date'], 'required'],

            [['reject_reason'], 'required', 'on' => self::SCENARIO_REJECT],
            [['appeal_reason'], 'required', 'on' => self::SCENARIO_APPEAL],
            [['date', 'number'], 'required', 'on' => self::SCENARIO_REQUISITES],
            [['total_payment_limit'], 'required', 'on' => self::SCENARIO_LIMIT],

            [['organization_id', 'payer_id', 'status'], 'integer'],
            [['date', 'date_dissolution'], 'safe'],
            [['reject_reason', 'appeal_reason', 'document_type'], 'string'],
            [[
                'number', 'document_path', 'document_base_url', 'additional_number', 'additional_date',
                'additional_document_base_url', 'additional_document_path'
            ], 'string', 'max' => 255],
            [
                ['organization_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Organization::class, 'targetAttribute' => ['organization_id' => 'id']
            ],
            [
                ['payer_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Payers::class, 'targetAttribute' => ['payer_id' => 'id']
            ],
            [['current_year_payment', 'total_payment', 'current_year_payment_limit', 'total_payment_limit'], 'number'],
            [['document', 'additionalDocument'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => UploadBehavior::class,
                'pathAttribute' => 'document_path',
                'baseUrlAttribute' => 'document_base_url',
                'attribute' => 'document',
            ],
            [
                'class' => UploadBehavior::class,
                'pathAttribute' => 'additional_document_path',
                'baseUrlAttribute' => 'additional_document_base_url',
                'attribute' => 'additionalDocument',
            ],
        ];
    }

    /**
     * Create new request
     */
    public function create()
    {
        $this->status = self::STATUS_NEW;
        $this->created_date = date('Y-m-d H:i:s');
    }

    /**
     * Set status to new
     */
    public function setNew()
    {
        $this->status = self::STATUS_NEW;
    }

    /**
     * Confirm request
     */
    public function confirm()
    {
        $this->status = self::STATUS_CONFIRMED;
        $this->created_date = date('Y-m-d H:i:s');
    }

    /**
     * Reject request
     */
    public function reject()
    {
        $this->status = self::STATUS_REJECTED;
    }

    /**
     * Appeal the rejection
     */
    public function appeal()
    {
        $this->status = self::STATUS_APPEALED;
    }

    /**
     * Activate cooperation
     */
    public function activate()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * получить ссылку на действующий документ соглашения
     *
     * @param string $fileStorage
     *
     * @return null|string
     */
    public function getActiveDocumentUrl($fileStorage = 'fileStorage')
    {
        if (is_null($this->document_base_url) || is_null($this->document_path) || !file_exists(Yii::$app->$fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $this->document_path)) {
            return null;
        }

        return $this->document_base_url . '/' . $this->document_path;
    }

    /**
     * получить ссылку на альтернативный документ соглашения
     *
     * @param string $fileStorage
     *
     * @return null|string
     */
    public function getAlternativeDocumentUrl($fileStorage = 'fileStorage')
    {
        /** @var OperatorSettings $operatorSettings */
        $operatorSettings = Yii::$app->operator->identity->settings;

        if (!is_null($this->total_payment_limit) && file_exists(Yii::$app->$fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $operatorSettings->general_document_path)) {
            return $operatorSettings->general_document_base_url . '/' . $operatorSettings->general_document_path;
        }

        if (is_null($this->total_payment_limit) && file_exists(Yii::$app->$fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $operatorSettings->extend_document_path)) {
            return $operatorSettings->extend_document_base_url . '/' . $operatorSettings->extend_document_path;
        }

        return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::class, ['id' => 'organization_id'])->inverseOf('cooperates');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayer()
    {
        return $this->hasOne(Payers::class, ['id' => 'payer_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'organization_id' => 'Organization ID',
            'payer_id' => 'Payer ID',
            'number' => 'Номер соглашения',
            'date' => ' Дата заключения соглашения',
            'date_dissolution' => 'Дата расторжения соглашения',
            'status' => 'Статус соглашения',
            'reject_reason' => 'Причина отказа',
            'appeal_reason' => 'Жалоба',
            'created_date' => 'Дата подачи заявки',
            'total_payment_limit' => 'Максимальная сумма',
            'period' => 'Период действия соглашения',
        ];
    }

    /**
     * @return array
     */
    public static function documentTypes()
    {
        return [
            self::DOCUMENT_TYPE_GENERAL => 'Договор без указания максимальной суммы',
            self::DOCUMENT_TYPE_EXTEND => 'Договор с указанием максимальной суммы',
            self::DOCUMENT_TYPE_CUSTOM => 'Свой',
        ];
    }

    /**
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_NEW => 'Новая',
            self::STATUS_ACTIVE => 'Активная',
            self::STATUS_REJECTED => 'Отклонена',
            self::STATUS_CONFIRMED => 'Подтверждён',
            self::STATUS_APPEALED => 'Обжалована',
        ];
    }

    /**
     * @return array
     */
    public static function documentNames()
    {
        return [
            self::DOCUMENT_NAME_FIRST => 'Договор о возмещении затрат',
            self::DOCUMENT_NAME_SECOND => 'Договор об оплате дополнительного образования',
        ];
    }

    /**
     * @return array
     */
    public static function documentNamesInGenitive()
    {
        return [
            self::DOCUMENT_NAME_FIRST => 'договора о возмещении затрат',
            self::DOCUMENT_NAME_SECOND => 'договора об оплате дополнительного образования',
        ];
    }

    /**
     * получить дату периода в которой находится указанная дата
     *
     * @param string $date
     *
     * @return int|null
     */
    public static function getPeriodFromDate($date)
    {
        /** @var OperatorSettings $operatorSettings */
        $operatorSettings = Yii::$app->operator->identity->settings;

        if (strtotime($date) >= strtotime($operatorSettings->current_program_date_from) && strtotime($date) <= strtotime($operatorSettings->current_program_date_to)) {
            return self::PERIOD_CURRENT;
        }

        if (strtotime($date) >= strtotime($operatorSettings->future_program_date_from) && strtotime($date) <= strtotime($operatorSettings->future_program_date_to)) {
            return self::PERIOD_FUTURE;
        }

        if (strtotime($date) < strtotime($operatorSettings->current_program_date_from)) {
            return self::PERIOD_ARCHIVE;
        }

        return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContracts()
    {
        return $this->hasMany(Contracts::class, ['payer_id' => 'payer_id', 'organization_id' => 'organization_id']);
    }

    /**
     * получить период действия соглашения
     */
    public function getPeriodValidityLabel()
    {
        return self::periodValidityLabelList($this->period);
    }

    /**
     * получить список периодов действия соглашения, если указан период возвращает указанный период
     *
     * @param int $period
     *
     * @return array|string
     */
    public static function periodValidityLabelList($period = null)
    {
        /** @var OperatorSettings $operatorSettings */
        $operatorSettings = Yii::$app->operator->identity->settings;

        $periodLabels = [
            self::PERIOD_CURRENT => 'с ' . \Yii::$app->formatter->asDate($operatorSettings->current_program_date_from) . ' по ' . \Yii::$app->formatter->asDate($operatorSettings->current_program_date_to),
            self::PERIOD_FUTURE => 'с ' . \Yii::$app->formatter->asDate($operatorSettings->future_program_date_from),
        ];

        return !is_null($period) ? $periodLabels[$period] : $periodLabels;
    }


















    //TODO всё убрать
    public function getInvoiceCooperatePayers()
    {
        if(!Yii::$app->user->isGuest) {

            $organizations = new Organization();
            $organization = $organizations->getOrganization();

            $rows = (new \yii\db\Query())
                ->select(['payer_id'])
                ->from('cooperate')
                ->where(['organization_id' => $organization['id']])
                ->andWhere(['status' => 1])
                ->column();
            
            $result = array();
            foreach ($rows as $payer_id) {
                $payer = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('invoices')
                    ->where(['organization_id' => $organization['id']])
                    ->andWhere(['payers_id' => $payer_id])
                    ->andWhere(['month' => date('m')-1])
                    ->andWhere(['prepayment' => 0])
                    ->andWhere(['status' => [0,1,2]])
                    ->column();
                
                if (!$payer) {
                    array_push($result, $payer_id);
                }
            }

            return $result;
        }
    }
    
    public function getDecinvoiceCooperatePayers()
    {
        if (!Yii::$app->user->isGuest) {

            $organizations = new Organization();
            $organization = $organizations->getOrganization();

            $rows = (new \yii\db\Query())
                ->select(['payer_id'])
                ->from('cooperate')
                ->where(['organization_id' => $organization['id']])
                ->andWhere(['status' => 1])
                ->column();
            
            $result = array();
            foreach ($rows as $payer_id) {
                $payer = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('invoices')
                    ->where(['organization_id' => $organization['id']])
                    ->andWhere(['payers_id' => $payer_id])
                    ->andWhere(['month' => 12])
                    ->andWhere(['prepayment' => 0])
                    ->andWhere(['status' => [0,1,2]])
                    ->column();
                
                if (!$payer) {
                    array_push($result, $payer_id);
                }
            }

            return $result;
        }
    }

    public function getPreInvoiceCooperatePayers()
    {
        if (!Yii::$app->user->isGuest) {
            $organizations = new Organization();
            $organization = $organizations->getOrganization();

            $rows = (new \yii\db\Query())
                ->select(['payer_id'])
                ->from('cooperate')
                ->where(['organization_id' => $organization['id']])
                ->andWhere(['status' => 1])
                ->column();
            
            $result = array();
            foreach ($rows as $payer_id) {
                $payer = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('invoices')
                    ->where(['organization_id' => $organization['id']])
                    ->andWhere(['payers_id' => $payer_id])
                    ->andWhere(['month' => date('m')])
                    ->andWhere(['prepayment' => 1])
                    ->andWhere(['status' => [0,1,2]])
                    ->column();
                
                if (!$payer) {
                    array_push($result, $payer_id);
                }
            }

            return $result;
        }
    }
    
    public function getCooperateOrg()
    {
        if (!Yii::$app->user->isGuest) {
        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();

            $rows = (new \yii\db\Query())
                ->select(['organization_id'])
                ->from('cooperate')
                ->where(['payer_id'=> $certificate['payer_id']])
                ->andWhere(['status'=> [0,1]])
                ->column();

            return array_unique($rows);
        }

        return null;
    }

    public static function findCooperateByParams($payerId, $organizationId, $period, $status = Cooperate::STATUS_ACTIVE)
    {
        $query = static::find()
            ->where([
                'payer_id' => $payerId,
                'organization_id' => $organizationId,
                'period' => $period,
            ])
            ->andFilterWhere(['status' => $status]);

        return $query->one();
    }
}
