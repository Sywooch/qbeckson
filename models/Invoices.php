<?php

namespace app\models;

use app\helpers\DeclinationOfMonths;
use mPDF;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "invoices".
 *
 * @property integer      $id
 * @property integer      $month
 * @property int          $year
 * @property integer      $organization_id
 * @property integer      $payers_id
 * @property integer      $sum
 * @property string       $number
 * @property string       $date
 * @property string       $link
 * @property integer      $prepayment
 * @property integer      $status
 * @property String       $statusAsString
 * @property int          $completeness
 * @property int          $cooperate_id
 * @property String       $contracts
 * @property string       $pdf
 *
 *
 * @property InvoiceHaveContract[] $invoiceHaveContracts
 * @property Contracts[] $contractModels
 * @property Contracts    $contract
 * @property Organization $organization
 * @property Payers       $payers
 * @property Payers       $payer
 */
class Invoices extends ActiveRecord
{
    const STATUS_NOT_VIEWED = 0;
    const STATUS_IN_THE_WORK = 1;
    const STATUS_PAID = 2;
    const STATUS_REMOVED = 3;

    const DIR_OF_PDF_REPORTS = "/uploads/invoices/";

    /**
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_NOT_VIEWED  => 'Не просмотрен',
            self::STATUS_IN_THE_WORK => 'В работе',
            self::STATUS_PAID        => 'Оплачен',
            self::STATUS_REMOVED     => 'Удален',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%invoices}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['month', 'organization_id', 'payers_id', 'completeness', 'prepayment', 'status', 'cooperate_id'], 'integer'],
            [['organization_id', 'payers_id', 'contracts', 'status'], 'required'],
            [['date'], 'safe'],
            [['number'], 'string'],
            [['sum'], 'number'],
            [['link', 'contracts', 'pdf'], 'string'],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
            [['payers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payers::className(), 'targetAttribute' => ['payers_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'month'           => 'Месяц, за который выставлен счет ',
            'organization_id' => 'Organization ID',
            'payers_id'       => 'Payers ID',
            'contracts'       => 'Контракты',
            'sum'             => 'Сумма счета',
            'number'          => 'Номер счета',
            'date'            => 'Дата счета',
            'link'            => 'Ссылка на документ ',
            'prepayment'      => 'Аванс',
            'completeness'    => 'ID полноты оказаных услуг',
            'status'          => 'Статус',
            'statusAsString'  => 'Статус',
            'payer'           => 'Плательщик',
        ];
    }

    /** @return string */
    public function getStatusAsString()
    {
        if (array_key_exists($this->status, self::statuses())) {
            return self::statuses()[$this->status];
        } else {
            return '---';
        }
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
        return $this->hasOne(Payers::className(), ['id' => 'payers_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayers()
    {
        return $this->hasOne(Payers::className(), ['id' => 'payers_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceHaveContracts()
    {
        return $this->hasMany(InvoiceHaveContract::className(), ['invoice_id' => 'id'])
            ->inverseOf('invoice');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractModels()
    {
        return $this->hasMany(Contracts::className(), ['id' => 'contract_id'])
            ->viaTable('invoice_have_contract', ['invoice_id' => 'id']);
    }


    /**
     * устанавливает соглашение счета
     *
     * @param boolean $preInvoice - предоплата
     */
    public function setCooperate($preInvoice = null)
    {
        if ($preInvoice) {
            $cooperate = Cooperate::findCooperateByParams($this->payers_id, $this->organization_id, Cooperate::getPeriodFromDate($this->date));
        } else {
            $cooperate = Cooperate::findOne(['payer_id' => $this->payers_id, 'organization_id' => $this->organization_id, 'status' => Cooperate::STATUS_ACTIVE]);
        }
        $this->cooperate_id = $cooperate->id;
    }


    public function setAsPaid()
    {
        $this->status = self::STATUS_PAID;
    }

    public function refoundMoney()
    {
        if ($completenesses = Completeness::findInvoicesByContracts(explode(',', $this->contracts), $this->month, date('Y', strtotime($this->date)))) {
            foreach ($completenesses as $completeness) {
                /** @var $certificate Certificates */
                $certificate = $completeness->contract->certificate;
                $date = join('-', [$completeness->year, $completeness->month, '01']);
                $monthlyPrice = $completeness->contract->getMonthlyPrice(strtotime($date));
                $difference = abs($monthlyPrice - $completeness->sum);
                $certificate->updateCounters(['balance' . $completeness->contract->periodSuffix => $difference]);
                $completeness->contract->updateCounters(['paid' => -1 * $difference]);
            }
        }
    }

    public static function getSummary($cooperateId)
    {
        $command = Yii::$app->db->createCommand("SELECT SUM(i.sum) as sum FROM invoices as i WHERE i.`prepayment` = 0 AND i.`status` = " . static::STATUS_PAID . " AND i.`cooperate_id`= :cooperate_id GROUP BY i.`cooperate_id`", [
            ':cooperate_id' => $cooperateId,
        ]);
        $sum = $command->queryScalar();

        $additionalSum = 0;
        $command = Yii::$app->db->createCommand("SELECT month, YEAR(`date`) as year FROM invoices as i WHERE i.`prepayment` = 0 AND i.`status` = " . static::STATUS_PAID . " AND i.`cooperate_id`= :cooperate_id", [
            ':cooperate_id' => $cooperateId,
        ]);
        if ($invoices = $command->queryAll()) {
            $command = Yii::$app->db->createCommand("SELECT SUM(i.sum) as sum FROM invoices as i WHERE i.`prepayment` = 1 AND i.`status` = " . static::STATUS_PAID . " AND i.`cooperate_id`= :cooperate_id AND i.`month` NOT IN (" . join(',', ArrayHelper::getColumn($invoices, 'month')) . ") GROUP BY i.`cooperate_id`", [
                ':cooperate_id' => $cooperateId,
                // TODO: Добавить условие на год
            ]);
            $additionalSum = $command->queryScalar();
        }

        return $sum + $additionalSum;
    }

    public function generatePrepaid()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $model = $this;

        $organization = Organization::findOne($model->organization_id);

        $cooperate = Cooperate::find()
            ->select(['number', 'date'])
            ->where(['payer_id' => $model->payers_id, 'organization_id' => $model->organization_id])
            ->andWhere(['cooperate.period' => Cooperate::getPeriodFromDate($model->date)])
            ->one();

        $date_invoice = explode("-", $model->date);
        $date_cooperate = explode("-", $cooperate['date']);

        $html = '<p style="text-align: center;">Приложение к счету от ' . $date_invoice[2] . '.' . $date_invoice[1] . '.' . $date_invoice[0] . ' №' . $model->number . '</p>';
        $html = $html . '<p style="text-align: center;">по договору ' . $cooperate['number'] . ' от ' . $date_cooperate[2] . '.' . $date_cooperate[1] . '.' . $date_cooperate[0] . '</p>';

        $m = DeclinationOfMonths::getMonthNameByNumberAsNominative((int)$model->month);

        $html = $html . '<p>Месяц, за который сформирован аванс: ' . $m . ' ' . date('Y') . '</p>';
        $html = $html . '<p>Наименование поставщика образовательных услуг: ' . $organization->name . '</p>';
        $html = $html . '<p>ОГРН/ОГРНИП поставщика образовательных услуг:  ' . $organization->OGRN . '</p>';
        $html = $html . '<p>Всего подлежит к оплате: ' . round($model->sum, 2) . ' руб.</p>';


        $html = $html . '<table border="1"  cellpadding="1" cellspacing="0">';
        $html = $html . '<tr>
        <td style="text-align: center;">&nbsp;№ п.п.&nbsp;</td>
        <td style="text-align: center;">&nbsp;№ договора&nbsp;</td>
        <td style="text-align: center;">&nbsp;Дата договора&nbsp;</td>
        <td style="text-align: center;">&nbsp;&nbsp;Номер сертификата&nbsp;&nbsp;</td>
        <td style="text-align: center;">&nbsp;Объем оказания<br>услуги, %&nbsp;</td>
        <td style="text-align: center;">&nbsp;К оплате, руб.&nbsp;</td>
        </tr>';

        $i = 1;
        foreach (explode(',', $model['contracts']) as $contracts) {
            $contract = Contracts::findOne($contracts);
            if (isset($contract)) {
                $date_contract = explode("-", $contract->date);

                $cert = Certificates::findOne($contract->certificate_id);

                $completeness = (new \yii\db\Query())
                    ->select(['completeness', 'sum'])
                    ->from('completeness')
                    ->where(['contract_id' => $contract->id])
                    ->andWhere(['month' => $model->month])
                    ->andWhere(['preinvoice' => 1])
                    ->one();

                $html = $html . '<tr>
            <td style="text-align: center;">' . $i++ . '</td>
            <td style="text-align: center;">' . $contract->number . '</td>
            <td style="text-align: center;">' . $date_contract[2] . '.' . $date_contract[1] . '.' . $date_contract[0] . '</td>
            <td style="text-align: center;">' . $cert->number . '</td>
            <td style="text-align: center;">' . $completeness["completeness"] . '</td>
            <td style="text-align: center;">' . round($completeness["sum"], 2) . '</td>
            </tr>';
            }
        }

        $html = $html . '</table>';

        $html = $html . '<br>
        <table width="100%" border="0"  cellpadding="1" cellspacing="0">
        <tr>
            <td >' . $organization->name . '</td>
        </tr>
        <tr>
            <td ><br>Руководитель<br><br><br><br>_________________/_________________/<br>М.П.</td>
            <td >Главный бухгалтер<br><br><br><br>_________________/_________________/</td>
        </tr>
        </table>';


        $mpdf = new mPDF();
        $mpdf->WriteHtml($html);
        $filename = 'prepaid-' . $model->number . '_' . $model->month . '-' . $model->year . '_' . $model->date . '_' . $model->organization_id . '.pdf';
        if (!file_exists(Yii::getAlias('@pfdoroot') . self::DIR_OF_PDF_REPORTS)) {
            Yii::trace(Yii::getAlias('@pfdoroot') . self::DIR_OF_PDF_REPORTS);
            mkdir(Yii::getAlias('@pfdoroot') . self::DIR_OF_PDF_REPORTS, 0777, true);
        }
        $mpdf->Output(Yii::getAlias('@pfdoroot') . self::DIR_OF_PDF_REPORTS . $filename, 'F');

        return self::DIR_OF_PDF_REPORTS . $filename;
    }

    public function generateInvoice()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $model = $this;

        $organization = Organization::findOne($model->organization_id);
        $prepaid = Invoices::find()
            ->select(['sum'])
            ->where(['payers_id' => $model->payers_id])
            ->andWhere(['organization_id' => $model->organization_id])
            ->andWhere(['month' => $model->month])
            ->andWhere(['prepayment' => 1])
            ->andWhere(['status' => [0, 1, 2]])
            ->one();

        $cooperate = Cooperate::find()
            ->select(['number', 'date'])
            ->where(['payer_id' => $model->payers_id, 'organization_id' => $model->organization_id])
            ->andWhere(['cooperate.period' => Cooperate::getPeriodFromDate($model->date)])
            ->one();

        $date_invoice = explode("-", $model->date);
        $date_cooperate = explode("-", $cooperate['date']);

        $html = '<p style="text-align: center;">Приложение к счету от ' . $date_invoice[2] . '.' . $date_invoice[1] . '.' . $date_invoice[0] . ' №' . $model->number . '</p>';
        $html = $html . '<p style="text-align: center;">по договору ' . $cooperate['number'] . ' от ' . $date_cooperate[2] . '.' . $date_cooperate[1] . '.' . $date_cooperate[0] . '</p>';

        $m = DeclinationOfMonths::getMonthNameByNumberAsNominative((int)$model->month);

        $html = $html . '<p>Месяц, за который сформирован счет: ' . $m . ' ' . date('Y') . '</p>';
        $html = $html . '<p>Наименование поставщика образовательных услуг: ' . $organization->full_name . '</p>';
        $html = $html . '<p>ОГРН/ОГРНИП поставщика образовательных услуг:  ' . $organization->OGRN . '</p>';

        $html = $html . '<p>Всего оказано услуг на сумму: ' . round($model->sum, 2) . ' руб.</p>';

        //return var_dump($prepaid);
        if ($prepaid['sum']) {
            $html = $html . '<p>Подлежит оплате: ' . round($model->sum - $prepaid['sum'], 2) . ' руб.</p>';
        } else {
            $html = $html . '<p>Подлежит оплате: ' . round($model->sum, 2) . ' руб.</p>';
        }


        $html = $html . '<table border="1"  cellpadding="1" cellspacing="0">';
        $html = $html . '<tr>
        <td style="text-align: center;">&nbsp;№ п.п.&nbsp;</td>
        <td style="text-align: center;">&nbsp;№ договора&nbsp;</td>
        <td style="text-align: center;">&nbsp;Дата договора&nbsp;</td>
        <td style="text-align: center;">&nbsp;&nbsp;Номер сертификата&nbsp;&nbsp;</td>
        <td style="text-align: center;">&nbsp;Объем оказания<br>услуги, %&nbsp;</td>
        <td style="text-align: center;">&nbsp;К оплате, руб.&nbsp;</td>
        </tr>';

        $i = 1;
        foreach (explode(',', $model['contracts']) as $contracts) {
            $contract = Contracts::findOne($contracts);
            $date_contract = explode("-", $contract->date);

            $cert = Certificates::findOne($contract->certificate_id);

            $completeness = (new \yii\db\Query())
                ->select(['completeness', 'sum'])
                ->from('completeness')
                ->where(['contract_id' => $contract->id])
                ->andWhere(['month' => $model->month])
                ->andWhere(['preinvoice' => 0])
                ->one();

            $sum = round($completeness['sum'], 2);

            $html = $html . '<tr>
            <td style="text-align: center;">' . $i++ . '</td>
            <td style="text-align: center;">' . $contract->number . '</td>
            <td style="text-align: center;">' . $date_contract[2] . '.' . $date_contract[1] . '.' . $date_contract[0] . '</td>
            <td style="text-align: center;">' . $cert->number . '</td>
            <td style="text-align: center;">' . $completeness["completeness"] . '</td>
            <td style="text-align: center;">' . $sum . '</td>
            </tr>';
        }

        $html = $html . '</table>';

        $html = $html . '<br>
        <table width="100%" border="0"  cellpadding="1" cellspacing="0">
        <tr>
            <td >' . $organization->name . '</td>
        </tr>
        <tr>
            <td ><br>Руководитель<br><br><br><br>_________________/_________________/<br>М.П.</td>
            <td >Главный бухгалтер<br><br><br><br>_________________/_________________/</td>
        </tr>
        </table>';

        $mpdf = new mPDF();
        $mpdf->WriteHtml($html);
        $filename = "invoice-" . $model->number . '_' . $model->month . '-' . $model->year . '_' . $model->date . '_' . $model->organization_id . '.pdf';
        if (!file_exists(Yii::getAlias('@pfdoroot') . self::DIR_OF_PDF_REPORTS)) {
            Yii::trace(Yii::getAlias('@pfdoroot') . self::DIR_OF_PDF_REPORTS);
            mkdir(Yii::getAlias('@pfdoroot') . self::DIR_OF_PDF_REPORTS, 0777, true);
        }
        $mpdf->Output(Yii::getAlias('@pfdoroot') . self::DIR_OF_PDF_REPORTS . $filename, 'F');

        return self::DIR_OF_PDF_REPORTS . $filename;
    }
}
