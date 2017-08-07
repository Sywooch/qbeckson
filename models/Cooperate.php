<?php

namespace app\models;

use Yii;
use app\models\Organization;
use app\models\Certificates;
use yii\behaviors\AttributeBehavior;
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
 *
 * @property mixed $cooperateOrganization
 * @property array $decinvoiceCooperatePayers
 * @property mixed $cooperatePayers
 * @property mixed $waitCooperatePayers
 * @property mixed $cooperateOrg
 * @property array $preInvoiceCooperatePayers
 * @property array $invoiceCooperatePayers
 * @property mixed $cooperateallPayers
 * @property mixed $cooperateWaitOrganization
 *
 * @property Organization $organization
 * @property Payers $payers
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cooperate';
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_REJECT] = $scenarios[self::SCENARIO_DEFAULT];
        $scenarios[self::SCENARIO_APPEAL] = $scenarios[self::SCENARIO_DEFAULT];
        $scenarios[self::SCENARIO_REQUISITES] = $scenarios[self::SCENARIO_DEFAULT];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['organization_id', 'payer_id', 'status', 'created_date'], 'required'],
            [['reject_reason'], 'required', 'on' => self::SCENARIO_REJECT],
            [['appeal_reason'], 'required', 'on' => self::SCENARIO_APPEAL],
            [['date', 'number'], 'required', 'on' => self::SCENARIO_REQUISITES],

            [['organization_id', 'payer_id', 'status'], 'integer'],
            [['date', 'date_dissolution'], 'safe'],
            [['reject_reason', 'appeal_reason'], 'string'],
            [['number'], 'string', 'max' => 255],
            [
                ['organization_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']
            ],
            [
                ['payer_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Payers::className(), 'targetAttribute' => ['payer_id' => 'id']
            ],
        ];

        return $rules;
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
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'organization_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayer()
    {
        return $this->hasOne(Payers::className(), ['id' => 'payer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayers()
    {
        return $this->hasOne(Payers::className(), ['id' => 'payer_id']);
    }

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

     public function getCooperateOrganization()
     {
        if (!Yii::$app->user->isGuest) {
            $payers = new Payers();
            $payer = $payers->getPayer();

            $rows = (new \yii\db\Query())
                ->select(['organization_id'])
                ->from('cooperate')
                ->where(['payer_id' => $payer['id']])
                ->andWhere(['status' => 1])
                ->column();

            return $rows;
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
