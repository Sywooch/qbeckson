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
 * @property integer $document_type
 * @property string $document_path
 * @property string $document_base_url
 *
 * @property Organization $organization
 * @property Payers $payers
 * @property null|string $documentUrl
 * @property Payers $payer
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

    const DOCUMENT_TYPE_GENERAL = 'general';
    const DOCUMENT_TYPE_EXTEND = 'extend';
    const DOCUMENT_TYPE_CUSTOM = 'custom';

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
        $scenarios[self::SCENARIO_REJECT] = $scenarios[self::SCENARIO_APPEAL] =
        $scenarios[self::SCENARIO_REQUISITES] =
            $scenarios[self::SCENARIO_DEFAULT];

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

            [['organization_id', 'payer_id', 'status', 'document_type'], 'integer'],
            [['date', 'date_dissolution'], 'safe'],
            [['reject_reason', 'appeal_reason'], 'string'],
            [['number', 'document_path', 'document_base_url'], 'string', 'max' => 255],
            [
                ['organization_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Organization::class, 'targetAttribute' => ['organization_id' => 'id']
            ],
            [
                ['payer_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Payers::class, 'targetAttribute' => ['payer_id' => 'id']
            ],
            [['document'], 'safe'],
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
     * @return null|string
     */
    public function getDocumentUrl()
    {
        return (null !== $this->document_path) ? $this->document_base_url . '/' . $this->document_path : null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::class, ['id' => 'organization_id']);
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
    }
}
