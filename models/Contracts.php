<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "contracts".
 *
 * @property integer $id
 * @property string $number
 * @property string $date
 * @property integer $certificate_id
 * @property integer $payer_id
 * @property integer $program_id
 * @property integer $year_id
 * @property integer $organization_id
 * @property integer $group_id
 * @property integer $status
 * @property string $status_termination
 * @property string $status_comment
 * @property integer $status_year
 * @property string $link_doc
 * @property string $link_ofer
 * @property double $all_funds
 * @property double $funds_cert
 * @property double $all_parents_funds
 * @property string $start_edu_programm
 * @property integer $funds_gone
 * @property string $stop_edu_contract
 * @property string $start_edu_contract
 * @property integer $sposob
 * @property integer $prodolj_d
 * @property integer $prodolj_m
 * @property integer $prodolj_m_user
 * @property double $first_m_price
 * @property double $other_m_price
 * @property double $first_m_nprice
 * @property double $other_m_nprice
 * @property string $change1
 * @property string $change2
 * @property string $change_org_fio
 * @property string $org_position
 * @property string $org_position_min
 * @property string $change_doctype
 * @property string $change_fioparent
 * @property string $change6
 * @property string $change_fiochild
 * @property string $change8
 * @property string $change9
 * @property string $change10
 * @property integer $ocen_fact
 * @property integer $ocen_kadr
 * @property integer $ocen_mat
 * @property integer $ocen_obch
 * @property integer $ocenka
 * @property integer $wait_termnate
 * @property string $date_termnate
 * @property string $url
 * @property double $cert_dol
 * @property double $payer_dol
 * @property double $rezerv
 * @property double $paid
 * @property integer $terminator_user
 * @property double $fontsize
 * @property float $parents_first_month_payment
 * @property float $parents_other_month_payment
 * @property float $payer_first_month_payment
 * @property float $payer_other_month_payment
 * @property integer $payment_order
 * @property integer $period
 * @property float $balance

 * @property Disputes[] $disputes
 * @property string $statusName
 * @property mixed $organizationname
 * @property mixed $invoices
 * @property string $yearyear
 * @property mixed $payers
 * @property mixed $certificatenumber
 * @property mixed $contracts
 * @property mixed $payersname
 * @property mixed $programname
 * @property mixed $year
 * @property Informs[] $informs
 *
 * @property Certificates $certificate
 * @property Organization $organization
 * @property Payers $payer
 * @property Programs $program
 * @property ProgrammeModule $module
 */
class Contracts extends \yii\db\ActiveRecord
{
    const STATUS_CREATED = 0;

    const STATUS_ACTIVE = 1;

    const STATUS_REFUSED = 2;

    const STATUS_ACCEPTED = 3;

    const STATUS_CLOSED = 4;

    public $certnumber;

    public $certfio;

    public $month_start_edu_contract;

    public $applicationIsReceived = 1;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contracts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['certificate_id', 'program_id', 'organization_id', 'status', 'status_year', 'funds_gone', 'group_id', 'year_id', 'sposob', 'prodolj_d', 'prodolj_m', 'prodolj_m_user', 'ocenka', 'wait_termnate', 'terminator_user', 'payment_order', 'period'], 'integer'],
            [['all_funds', 'funds_cert', 'all_parents_funds', 'first_m_price', 'other_m_price', 'first_m_nprice', 'other_m_nprice', 'ocen_fact', 'ocen_kadr', 'ocen_mat', 'ocen_obch', 'cert_dol', 'payer_dol', 'rezerv', 'paid', 'fontsize', 'balance', 'payer_first_month_payment', 'payer_other_month_payment', 'parents_other_month_payment', 'parents_first_month_payment'], 'number'],
            [['date', 'status_termination', 'start_edu_programm', 'stop_edu_contract', 'start_edu_contract', 'date_termnate'], 'safe'],
            ['date', 'validateDate'],
            [['status_comment', 'number', 'certnumber', 'certfio', 'change1', 'change2', 'change_org_fio', 'org_position', 'org_position_min', 'change_doctype', 'change_fioparent', 'change6', 'change_fiochild', 'change8', 'change9', 'change10', 'month_start_edu_contract', 'url'], 'string'],
            ['applicationIsReceived', 'required', 'requiredValue' => 1, 'message' => false],
            [['certificate_id', 'program_id', 'organization_id'], 'required'],
            [['link_doc', 'link_ofer'], 'string', 'max' => 255],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Programs::className(), 'targetAttribute' => ['program_id' => 'id']],
            [['payer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payers::className(), 'targetAttribute' => ['payer_id' => 'id']],
            [['certificate_id'], 'exist', 'skipOnError' => true, 'targetClass' => Certificates::className(), 'targetAttribute' => ['certificate_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Groups::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['year_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgrammeModule::className(), 'targetAttribute' => ['year_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Номер договора',
            'date' => 'Дата договора',
            'certificate_id' => 'Номер сертификата',
            'payer_id' => 'Плательщик',
            'program_id' => 'Программа',
            'year_id' => 'Год',
            'organization_id' => 'ID Организации',
            'group_id' => 'Шифр группы',
            'status' => 'Статус договора',
            'status_termination' => 'Дата прекращения действия договора',
            'status_comment' => 'Причина',
            'status_year' => 'Причина',
            'link_doc' => 'Ссылка на договор',
            'link_ofer' => 'Ссылка на оферту',
            'all_funds' => 'Cовокупный объем средств, необходимый для оплаты договора',
            'funds_cert' => 'Объем платежей, покрываемый за счет сертификата',
            'all_parents_funds' => 'Объем платежей, покрываемый за счет родителей',
            'start_edu_programm' => 'Дата начала обучения по программе',
            'funds_gone' => 'Объем средств, ушедших в уплату договора ',
            'start_edu_contract' => 'Дата начала обучения по договору',
            'month_start_edu_contract' => 'Месяц начала обучения по договору',
            'stop_edu_contract' => 'Дата окончания обучения по договору',
            'certnumber' => 'Номер сертификата',
            'certfio' => 'ФИО ребенка',
            'sposob' => 'Заказчик осуществляет оплату',
            'prodolj_d' => 'Продолжительность дней',
            'prodolj_m' => 'Продолжительность месяцев',
            'prodolj_m_user' => 'Продолжительность месяцев ученика',
            'first_m_price' => 'Цена первого месяца',
            'other_m_price' => 'Цена остальных месяцев',
            'first_m_nprice' => 'Нормативная цена первого месяца',
            'other_m_nprice' => 'Нормативная цена остальных месяцев',
            'change1' => '1 поле с "ая"',
            'change2' => '2 поле с "ая"',
            'change_org_fio' => 'Поле с фио представителя организации',
            'org_position' => 'Должность представителя организации',
            'org_position_min' => 'Должность представителя организации (кратко)',
            'change_doctype' => 'Поле с типом документа',
            'change_fioparent' => 'Поле с фио родителя',
            'change6' => '6 поле с "ая"',
            'change_fiochild' => 'Поле с фио ребенка',
            'change8' => '8 поле с "ая"',
            'change9' => '9 поле с "ая"',
            'change10' => '10 поле с "го"',
            'ocen_fact' => 'Оценка достижения заявленных результатов',
            'ocen_kadr' => 'Оценка выполнения кадровых требований',
            'ocen_mat' => 'Оценка выполнения требований к средствам обучения',
            'ocen_obch' => 'Оценка общей удовлетворенности программой',
            'ocenka' => 'Наличие оценки',
            'wait_termnate' => 'Ожидает расторжения',
            'date_termnate' => 'Дата расторжения',
            'cert_dol' => 'Доля сертификата',
            'payer_dol' => 'Доля плательщика',
            'rezerv' => 'Зарезервированно средств',
            'paid' => 'Оплачено',
            'terminator_user' => 'Инициатор расторжения',
            'fontsize' => 'Размер шрифта',
            'certificatenumber' => 'Номер сертификата',
            'payment_order' => 'Порядок оплаты',
            'applicationIsReceived' => 'заявление от Заказчика получено',
        ];
    }

    public function validateDate($attribute, $params)
    {
        //print_r(strtotime($this->$attribute)); echo ' -- ';
        //print_r(strtotime($this->start_edu_contract));exit;
        if (strtotime($this->$attribute) > strtotime($this->start_edu_contract)) {
            $this->addError($attribute, 'Дата договора не может превышать дату начала действия договора.');
        }
    }

    public function getPayer()
    {
        return $this->hasOne(Payers::className(), ['id' => 'payer_id']);
    }
    
    public function getPayers()
    {
        return $this->hasOne(Payers::className(), ['id' => 'payer_id']);
    }
    
    public function getPayersname()
    {        
        $payer = (new \yii\db\Query())
                            ->select(['id', 'name'])
                            ->from('payers')
                            ->where(['id' => $this->payer_id])
                            ->one();

         return Html::a($payer['name'], Url::to(['/payers/view', 'id' => $payer['id']]), ['class' => 'blue', 'target' => '_blank']);
    }
             
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificate()
    {
        return $this->hasOne(Certificates::className(), ['id' => 'certificate_id']);
    }
    
    public function getCertificatenumber()
    {
         $certificate = (new \yii\db\Query())
            ->select(['id', 'number'])
            ->from('certificates')
            ->where(['id' => $this->certificate_id])
            ->one();
        
         //return $certificate['number'];
        
        return Html::a($certificate['number'], Url::to(['/certificates/view', 'id' => $certificate['id']]), ['class' => 'blue', 'target' => '_blank']);
    }
    
    public function getGroup()
    {
        return $this->hasOne(Groups::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'organization_id']);
    }

    public function getOrganizationname()
    {
          $organization = (new \yii\db\Query())
                            ->select(['id', 'name'])
                            ->from('organization')
                            ->where(['id' => $this->organization_id])
                            ->one();


                    return Html::a($organization['name'], Url::to(['/organization/view', 'id' => $organization['id']]), ['class' => 'blue', 'target' => '_blank']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Programs::className(), ['id' => 'program_id']);
    }
   
    public function getProgramname()
    {        
         $program = (new \yii\db\Query())
                ->select(['id', 'name'])
                ->from('programs')
                ->where(['id' => $this->program_id])
                ->one();

        return Html::a($program['name'], Url::to(['/programs/view', 'id' => $program['id']]), ['class' => 'blue', 'target' => '_blank']);
    }
    
    
    public function getYear()
    {
        return $this->hasOne(ProgrammeModule::className(), ['id' => 'year_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(ProgrammeModule::className(), ['id' => 'year_id']);
    }

    public function getYearyear()
    {      
        $year = (new \yii\db\Query())
                ->select(['id', 'year'])
                ->from('years')
                ->where(['id' => $this->year_id])
                ->one();
        
        if ($year['year'] == 1) { return 'Первый';}
        if ($year['year'] == 2) { return 'Второй';}
        if ($year['year'] == 3) { return 'Третий';}
        if ($year['year'] == 4) { return 'Четвертый';}
        if ($year['year'] == 5) { return 'Пятый';}
        if ($year['year'] == 6) { return 'Шестой';}
        if ($year['year'] == 7) { return 'Седьмой';}
    }

    public function preinvoiceCompleteness() {
        $completeness = (new \yii\db\Query())
                            ->select(['completeness'])
                            ->from('completeness')
                            ->where(['contract_id' => $this->id])
                            ->andWhere(['month' => 2])
                            ->andWhere(['preinvoice' => 1])
                            ->one();
        return $completeness['completeness'];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDisputes()
    {
        return $this->hasMany(Disputes::className(), ['contract_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInforms()
    {
        return $this->hasMany(Informs::className(), ['contract_id' => 'id']);
    }
    
    public function getInvoices()
    {
        return $this->hasMany(Invoices::className(), ['contract_id' => 'id']);
    }

    public function getContracts() {

        $query = Contracts::find();

        if(!Yii::$app->user->isGuest) {
            $query->where(['user_id' => Yii::$app->user->id]);
        }

        return $query->one();
    }

    public function getContractsProgram($status) {

        if(!Yii::$app->user->isGuest) {

            $certificates = new Certificates();
            $certificate = $certificates->getCertificates();

            $rows = (new \yii\db\Query())
                ->select(['program_id'])
                ->from('contracts')
                ->where(['certificate_id' => $certificate['id']])
                ->andWhere(['status' => $status])
                ->column();

            return $rows;
        }
    }
    
    public function getContractsYear($id) {
        if(!Yii::$app->user->isGuest) {
            $certificates = new Certificates();
            $certificate = $certificates->getCertificates();

            $rows = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['certificate_id' => $certificate['id']])
                ->andWhere(['year_id' => $id])
                ->andWhere(['status' => [0,1,3]])
                ->count();

            return $rows;
        }
    }

    /**
     * DEPRECATED
     * Use getStatusName() instead
     */
    public function statusName($id)
    {
        if ($id == 0) { return 'Создан, ожидает подтверждения'; }
        if ($id == 1) { return 'Действует'; }
        if ($id == 2) { return 'Отклонен без заключения'; }
        if ($id == 3) { return 'Подтвержден, ожидает заключения'; }
        if ($id == 4) { return 'Прекратил действие'; }
    }

    public function getFullUrl()
    {
        return Url::to('/', true) . 'uploads/contracts/' . $this->url;
    }

    public function getStatusName()
    {
        $statusName = '';
        switch ($this->status) {
            case self::STATUS_CREATED:
                $statusName = 'Создан, ожидает подтверждения';
                break;
            case self::STATUS_ACTIVE:
                $statusName = 'Действует';
                break;
            case self::STATUS_REFUSED:
                $statusName = 'Отклонен без заключения';
                break;
            case self::STATUS_ACCEPTED:
                $statusName = 'Подтвержден, ожидает заключения';
                break;
            case self::STATUS_CLOSED:
                $statusName = 'Прекратил действие';
        }

        return $statusName;
    }

    /**
     * DEPRECATED
     * Use app\helpers\FormattingHelper::asSpelloutOrdinal() instead
     */
    public function yearName($data) {
        if ($data == '1') { return 'Первый';}
        if ($data == '2') { return 'Второй';}
        if ($data == '3') { return 'Третий';}
        if ($data == '4') { return 'Четвертый';}
        if ($data == '5') { return 'Пятый';}
        if ($data == '6') { return 'Шестой';}
        if ($data == '7') { return 'Седьмой';}
    }

    public static function getCountUsedCertificates($amountPerCertificate = null, $params = []) {
        $query = "SELECT count(*) FROM `contracts` CROSS JOIN `payers` ON `contracts`.payer_id = `payers`.id WHERE status=:status AND `payers`.operator_id = " . Yii::$app->operator->identity->id;
        $groupBy = "GROUP BY certificate_id";

        if (!empty($amountPerCertificate) && $operation = substr($amountPerCertificate, 0, 1)) {
            $groupBy .= " HAVING count(certificate_id) " . $operation . " " . substr($amountPerCertificate, 1);
        }
        if (!empty($params['payerId'])) {
            $query .= " AND payer_id = " . $params['payerId'];
        }
        if (!empty($params['organizationId'])) {
            $query .= " AND organization_id = " . $params['organizationId'];
        }

        $query = "SELECT count(*) as cnt FROM (" . $query . " " . $groupBy . ") as t";
        $command = Yii::$app->db->createCommand($query, [':status' => 1]);
        $result = $command->queryOne();

        return $result['cnt'];
    }

    public static function getCountContracts($params = []) {
        $query = static::find()
            ->joinWith(['payers'])
            ->where('`payers`.operator_id = ' . Yii::$app->operator->identity->id);

        if (empty($params['status'])) {
            $query->andWhere(['status' => 1]);
        } else {
            $query->andWhere(['status' => $params['status']]);
        }

        $query->andFilterWhere(['payer_id' => $params['payerId']]);
        $query->andFilterWhere(['certificate_id' => $params['certificateId']]);
        $query->andFilterWhere(['organization_id' => $params['organizationId']]);

        return $query->count();
    }

    /**
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_CREATED => 'Ожидает',
            self::STATUS_ACTIVE => 'Действующий',
            self::STATUS_REFUSED => 'Отклонён',
            self::STATUS_ACCEPTED => 'Подтверждён',
            self::STATUS_CLOSED => 'Расторгнут',
        ];
    }

    /**
     * @return array
     */
    public static function sposobs()
    {
        return [
            1 => 'за наличный расчет',
            2 => 'в безналичном порядке на счет Исполнителя'
        ];
    }

    /**
     * @return array
     */
    public static function paymentOrders()
    {
        return [
            1 => 'Оплата Заказчиком осуществляется вне зависимости от посещения занятий ребёнком за каждый месяц действия договора',
            2 => 'Оплата за месяц действия договора Заказчиком осуществляется пропорционально доле посещённых ребёнком занятий'
        ];
    }
}
