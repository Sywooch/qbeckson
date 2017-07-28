<?php

namespace app\models;

use Yii;
use app\models\Organization;
use app\models\Certificates;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "cooperate".
 *
 * @property integer $id
 * @property integer $organization_id
 * @property integer $payer_id
 * @property string $date
 * @property string $date_dissolution
 * @property integer $status
 * @property string $reade
 * @property string $number
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
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cooperate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['organization_id', 'payer_id', 'date', 'status'], 'required'],
            [['organization_id', 'payer_id', 'status'], 'integer'],
            [['date', 'date_dissolution'], 'safe'],
            [['number'], 'string', 'max' => 255],
            //[['number'], 'unique'],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
            [['payer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payers::className(), 'targetAttribute' => ['payer_id' => 'id']],
        ];
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
            'status' => 'Cтатус соглашения',
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
    
    /*public function getContractsCount($payer, $org)
    {
          return = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('contracts')
                        ->where(['payer_id' => $payer])
                        ->andWhere(['organization_id' => $org])
                        ->count();
    } */

    public function getCooperatePayers() {

        if(!Yii::$app->user->isGuest) {

            $organizations = new Organization();
            $organization = $organizations->getOrganization();

            $rows = (new \yii\db\Query())
                ->select(['payer_id'])
                ->from('cooperate')
                ->where(['organization_id' => $organization['id']])
                ->andWhere(['status' => 1])
                ->column();

            return $rows;
        }
    }
    
    public function getInvoiceCooperatePayers() {

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
    
    public function getDecinvoiceCooperatePayers() {

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
    
    
    public function getPreInvoiceCooperatePayers() {

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

    public function getWaitCooperatePayers() {

        if(!Yii::$app->user->isGuest) {

            $organizations = new Organization();
            $organization = $organizations->getOrganization();

            $rows = (new \yii\db\Query())
                ->select(['payer_id'])
                ->from('cooperate')
                ->where(['organization_id' => $organization['id']])
                ->andWhere(['status' => 0])
                ->column();

            return $rows;
        }
    }



    public function getCooperateallPayers() {

        if(!Yii::$app->user->isGuest) {

            $organizations = new Organization();
            $organization = $organizations->getOrganization();

            $rows = (new \yii\db\Query())
                ->select(['payer_id'])
                ->from('cooperate')
                ->where(['organization_id' => $organization['id']])
                ->andWhere(['status' => [0,1]])
                ->column();

            return $rows;
        }
    }

     public function getCooperateOrganization() {

        if(!Yii::$app->user->isGuest) {

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

    public function getCooperateWaitOrganization() {

        if(!Yii::$app->user->isGuest) {

            $payers = new Payers();
            $payer = $payers->getPayer();

            $rows = (new \yii\db\Query())
                ->select(['organization_id'])
                ->from('cooperate')
                ->where(['payer_id' => $payer['id']])
                ->andWhere(['status' => 0])
                ->column();

            return $rows;
        }
    }
    
    public function getCooperateOrg() {

        if(!Yii::$app->user->isGuest) {
        
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
