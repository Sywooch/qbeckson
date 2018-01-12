<?php

namespace app\models\contracts;

use app\helpers\CalculationHelper;
use app\helpers\FormattingHelper;
use app\models\Contracts;
use app\models\Cooperate;
use app\models\OperatorSettings;
use mPDF;
use Yii;

/**
 * заявка договора
 */
class ContractRequest
{
    /**
     * дата начала действия контракта
     *
     * @var string
     */
    private $start_edu_contract;

    /**
     * дата окончания действия контракта
     *
     * @var string
     */
    private $stop_edu_contract;

    /**
     * @var string
     */
    private $realizationPeriod;

    /**
     * сообщение при ошибки валидации
     *
     * @var string
     */
    public $errorMessage;

    /**
     * @var OperatorSettings
     */
    private $operatorSettings;

    /**
     * валидировать данные
     *
     * @param $groupDateStart
     * @param $groupDateStop
     * @param $canUseFutureBalance
     * @param $canUseCurrentBalance
     *
     * @return bool
     */
    public function validate($groupDateStart, $groupDateStop, $canUseCurrentBalance, $canUseFutureBalance)
    {
        if (!isset($this->start_edu_contract)) {
            $this->errorMessage = 'Необходимо указать начало действия контракта.';

            return false;
        }

        // проверить нахождение даты начала обучения в пределах периода реализации программы в группе
        if ((strtotime($this->start_edu_contract) < strtotime($groupDateStart) || strtotime($this->start_edu_contract) > strtotime($groupDateStop))) {
            $this->errorMessage = 'Дата начала обучения должна быть в пределах срока реализации программы в группе: ' . \Yii::$app->formatter->asDate($groupDateStart) . ' - ' . \Yii::$app->formatter->asDate($groupDateStop) . '.';

            return false;
        }

        if (!$canUseCurrentBalance && 1 == $canUseFutureBalance &&
            (strtotime($this->start_edu_contract) < strtotime($this->getOperatorSettings()->future_program_date_from) || strtotime($this->start_edu_contract) > strtotime($groupDateStop))) {
            if (strtotime($this->getOperatorSettings()->future_program_date_from) > strtotime($groupDateStop)) {
                $this->errorMessage = 'В настоящее время запись недоступна.';

                return false;
            }

            $this->errorMessage = 'Дата начала обучения по договору должна быть в пределах: ' . \Yii::$app->formatter->asDate($this->getOperatorSettings()->future_program_date_from) . ' - ' . \Yii::$app->formatter->asDate($groupDateStop) . ', поскольку уполномоченная организация закрыла возможность зачисления в текущем периоде.';

            return false;
        }

        if ($canUseCurrentBalance && 1 != $canUseFutureBalance &&
            (strtotime($this->start_edu_contract) > strtotime($this->getOperatorSettings()->current_program_date_to))) {
            $this->errorMessage = 'Дата начала обучения по договору должна быть в пределах: ' . \Yii::$app->formatter->asDate($this->getOperatorSettings()->current_program_date_from) . ' - ' . \Yii::$app->formatter->asDate($this->getOperatorSettings()->current_program_date_to) . ', пока уполномоченная организация не установила возможность зачисления в будущем периоде.';

            return false;
        }

        if ($canUseCurrentBalance && 1 == $canUseFutureBalance &&
            (strtotime($this->start_edu_contract) < strtotime($groupDateStart) || strtotime($this->start_edu_contract) > strtotime($groupDateStop))) {
            $this->errorMessage = 'Дата начала обучения должна быть в пределах срока реализации программы в группе: ' . \Yii::$app->formatter->asDate($groupDateStart) . ' - ' . \Yii::$app->formatter->asDate($groupDateStop) . '.';

            return false;
        }

        if (strtotime($this->start_edu_contract) >= strtotime($this->getOperatorSettings()->current_program_date_from) &&
            strtotime($this->start_edu_contract) <= strtotime($this->getOperatorSettings()->current_program_date_to)
        ) {
            $this->setRealizationPeriod(Contracts::CURRENT_REALIZATION_PERIOD);
            $this->stop_edu_contract = $this->getOperatorSettings()->current_program_date_to;
            if (strtotime($groupDateStop) < strtotime($this->getOperatorSettings()->current_program_date_to)) {
                $this->stop_edu_contract = $groupDateStop;
            }
        }

        if (strtotime($this->start_edu_contract) >= strtotime($this->getOperatorSettings()->future_program_date_from) &&
            strtotime($this->start_edu_contract) <= strtotime($this->getOperatorSettings()->future_program_date_to)
        ) {
            $this->setRealizationPeriod(Contracts::FUTURE_REALIZATION_PERIOD);
            $this->stop_edu_contract = $this->getOperatorSettings()->future_program_date_to;
            if (strtotime($groupDateStop) < strtotime($this->getOperatorSettings()->future_program_date_to)) {
                $this->stop_edu_contract = $groupDateStop;
            }
        }

        if (null === $this->getRealizationPeriod()) {
            $this->errorMessage = 'В данный период времени реализация программы не осуществляется.';

            return false;
        }

        if ($this->start_edu_contract === $this->stop_edu_contract) {
            $this->errorMessage = 'Даты должны отличаться';

            return false;
        }

        if (strtotime($this->start_edu_contract) >= strtotime($this->stop_edu_contract)) {
            $this->errorMessage = 'Дата окончания обучения по договору должна быть больше даты начала обучения';

            return false;
        }

        return true;
    }

    /**
     * получить данные заявки (контракт со статусом Contracts::STATUS_REQUESTED)
     * ---
     * порядок произведения расчетов:
     * Общий:
     * 1) Считаем число дней всего периода реализации группы = дата окончания группы – дата начала группы + 1
     *
     * 2)Считаем, охватывает ли выбранный период обучения более чем один месяц (период с 1 ноября по 30 ноября
     * охватывает всего один месяц, в то время как с 31 октября по 1 ноября, двухдневный, охватывает два месяца).
     * Если период охватывает только один месяц, то считаем нормативную стоимость и цену как число дней в выбранном
     * для освоения периоде
     * (дата конца выбранного периода – дата начала выбранного периода+1)/результат первого действия*НС(ЦЕНА).
     * Округляем до копеек вниз.
     *
     * 3) Если более одного месяца:Считаем сколько дней в первом месяце выбранного периода. Рассчитываем нормативную
     * стоимость и цену программы применительно к первому месяцу
     * (делим число дней в первом месяце (число дней месяца – дата начала +1)
     * выбранного периода на результат расчета первого действия).
     *
     * 4) Считаем ежемесячную округленную нормативную стоимость и цены оставшихся месяцев:
     * (дата окончания выбранного периода – первый день второго месяца реализации +1)/
     * результаты расчета первого действия*НС(ЦЕНА)/число месяцев в периоде (кроме первого) и округляем до копеек вверх.
     * После чего суммируем обратно.
     *
     * 5) Считаем полную нормативную стоимость и цену выбранного периода:
     * (дата конца выбранного периода – дата начала выбранного периода+1)/результаты расчета первого действия*НС(ЦЕНА).
     * Округляем до копеек вниз.
     *
     * 6)Считаем нормативную стоимость и цену первого месяца:
     * Результат расчета 5-го действия – результат суммирования 4-го действия.
     *
     * В результате мы имеем все параметры договора кроме увязки с остатками сертификата…
     * All_funds = цена первого месяца + цена прочего месяца*число прочих месяцев
     * Вариант А: funds_cert = min (нормативная стоимость первого месяца + нормативная стоимость прочего
     * месяца*число прочих месяцев; остаток сертификата текущего периода)
     * Вариант Б: funds_cert = min (нормативная стоимость первого месяца + нормативная стоимость прочего
     * месяца*число прочих месяцев; остаток сертификата будущего периода)
     * all_parents_funds= All_funds - funds_cert
     * first_m_price – цена первого месяца
     * other_m_price – цена прочего месяца (0 – если всего один месяц)
     * first_m_nprice – нормативная стоимость первого месяца (0 – если всего один месяц) other_m_nprice -
     * нормативная стоимость прочего месяца
     * Подтверждение заявки: вот тут вот будут расчеты влиять на остатки и резервы текущего и будущего периодов
     *
     * @param $groupDateStart
     * @param $groupDateStop
     * @param $groupModulePrice
     * @param $groupModuleNormativePrice
     * @param $groupId
     * @param $groupProgramId
     * @param $groupYearId
     * @param $groupOrganizationId
     * @param $certificateId
     * @param $certificatePayerId
     * @param $certificateNumber
     * @param $certificateBalance
     * @param $certificateBalanceF
     *
     * @return array|null
     */
    public function getData($groupDateStart, $groupDateStop, $groupModulePrice, $groupModuleNormativePrice, $groupId, $groupProgramId, $groupYearId, $groupOrganizationId, $certificateId, $certificatePayerId, $certificateNumber, $certificateBalance, $certificateBalanceF)
    {
        // Период реализации в месяцах
        $realizationPeriodInMonths = CalculationHelper::monthesInPeriod($this->start_edu_contract, $this->stop_edu_contract);

        // Период реализации в днях
        $realizationPeriodInDays = CalculationHelper::daysBetweenDates($this->start_edu_contract, $this->stop_edu_contract);
        // Период реализации группы в днях
        $groupRealizationPeriod = CalculationHelper::daysBetweenDates($groupDateStart, $groupDateStop);
        // Всего необходимо заплатить
        $all_funds = CalculationHelper::roundTo(
            $realizationPeriodInDays / $groupRealizationPeriod * $groupModulePrice
        );
        // Нормативная стоимость
        $normativePrice = CalculationHelper::roundTo(
            $realizationPeriodInDays / $groupRealizationPeriod * $groupModuleNormativePrice
        );
        // Меньшее из трёх all_funds / $normativePrice / остаток по сертификату
        if ($this->getRealizationPeriod() == Contracts::CURRENT_REALIZATION_PERIOD) {
            $balance = $certificateBalance;
        } else {
            $balance = $certificateBalanceF;
        }
        $funds_cert = min($all_funds, $normativePrice, $balance);
        // Сколько надо заплатить родителям
        $all_parents_funds = $all_funds - $funds_cert;
        // Доля сертификата
        $cert_dol = $all_parents_funds / $all_funds;
        // Доля плательщика
        $payer_dol = 1 - $cert_dol;
        //Если кто-во месяцев реализации > 1

        if ($realizationPeriodInMonths > 1) {
            // Дней в первом месяце
            $daysInFirstMonth = CalculationHelper::daysBetweenDates(
                $this->start_edu_contract,
                date('Y-m-t', strtotime($this->start_edu_contract))
            );
            $otherMonthsPricePerMonth = CalculationHelper::roundTo(
                ($realizationPeriodInDays - $daysInFirstMonth) /
                $groupRealizationPeriod * $groupModulePrice / ($realizationPeriodInMonths - 1),
                CalculationHelper::TO_DOWN
            );
            $otherMonthsNormativePricePerMonth = CalculationHelper::roundTo(
                ($realizationPeriodInDays - $daysInFirstMonth) /
                $groupRealizationPeriod * $groupModuleNormativePrice / ($realizationPeriodInMonths - 1),
                CalculationHelper::TO_DOWN
            );
            $firstMonthPrice = $all_funds - $otherMonthsPricePerMonth * ($realizationPeriodInMonths - 1);
            $firstMonthNormativePrice = $normativePrice -
                $otherMonthsNormativePricePerMonth * ($realizationPeriodInMonths - 1);

            if ($all_parents_funds > 0) {
                $parents_other_month_payment = CalculationHelper::roundTo(
                    ($realizationPeriodInDays - $daysInFirstMonth) /
                    $realizationPeriodInDays * $all_parents_funds / ($realizationPeriodInMonths - 1),
                    CalculationHelper::TO_DOWN
                );
                $parents_first_month_payment = $all_parents_funds - $parents_other_month_payment * ($realizationPeriodInMonths - 1);
            }
            if ($funds_cert > 0) {
                $payer_other_month_payment = CalculationHelper::roundTo(
                    ($realizationPeriodInDays - $daysInFirstMonth) /
                    $realizationPeriodInDays * $funds_cert / ($realizationPeriodInMonths - 1),
                    CalculationHelper::TO_DOWN
                );
                $payer_first_month_payment = round($funds_cert - $payer_other_month_payment * ($realizationPeriodInMonths - 1), 2);
            }
        }

        $contractData = [
            'certificate_id' => $certificateId,
            'payer_id' => $certificatePayerId,
            'group_id' => $groupId,
            'program_id' => $groupProgramId,
            'year_id' => $groupYearId,
            'organization_id' => $groupOrganizationId,
            'start_edu_contract' => date('Y-m-d', strtotime($this->start_edu_contract)),
            'stop_edu_contract' => date('Y-m-d', strtotime($this->stop_edu_contract)),
            'prodolj_d' => $realizationPeriodInDays,
            'prodolj_m' => $realizationPeriodInMonths,
            'prodolj_m_user' => $realizationPeriodInMonths,
            'all_funds' => $all_funds,
            'funds_cert' => $funds_cert,
            'all_parents_funds' => $all_parents_funds,
            'cert_dol' => $cert_dol,
            'payer_dol' => $payer_dol,
            'first_m_price' => $firstMonthPrice ?? $all_funds,
            'first_m_nprice' => $firstMonthNormativePrice ?? $normativePrice,
            'other_m_price' => $otherMonthsPricePerMonth ?? 0,
            'other_m_nprice' => $otherMonthsNormativePricePerMonth ?? 0,
            'parents_first_month_payment' => $parents_first_month_payment ?? $all_parents_funds,
            'parents_other_month_payment' => $parents_other_month_payment ?? 0,
            'payer_first_month_payment' => $payer_first_month_payment ?? $funds_cert,
            'payer_other_month_payment' => $payer_other_month_payment ?? 0,
            'url' => $certificateNumber . '-' . Yii::$app->security->generateRandomString(4) . '.pdf',
            'sposob' => 2,
            'payment_order' => 1,
            'balance' => $balance,
            'period' => $this->getRealizationPeriod(),
        ];

        return $contractData;
    }

    /**
     * создать договор оферты
     *
     * @param Contracts $contract
     *
     * @return mPDF
     */
    public function makePdfForContract($contract)
    {
        if (!$contract) {
            return null;
        }

        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $organization = $contract->organization;
        $program = $contract->program;
        $group = $contract->group;
        $year = $contract->year;
        $payer = $contract->payer;

        $date_elements_user = explode("-", $contract->start_edu_contract);

        $cooperate = Cooperate::find()->select(['number', 'date'])->where(['id' => $contract->cooperate_id])->one();
        $date_cooperate = explode("-", $cooperate['date']);

        if ($program->form == 1) {
            $programform = 'Очная';
        }
        if ($program->form == 2) {
            $programform = 'Очно-заочная';
        }
        if ($program->form == 3) {
            $programform = 'Заочная';
        }
        if ($program->form == 4) {
            $programform = 'Очная с применением дистанционных технологий и/или электронного обучения';
        }
        if ($program->form == 5) {
            $programform = 'Очно-заочная с применением дистанционных технологий и/или электронного обучения';
        }
        if ($program->form == 6) {
            $programform = 'Заочная с применением дистанционных технологий и/или электронного обучения';
        }

        $headerText = $organization->contractSettings->header_text;
        $headerText = str_replace(
            '№0000000000',
            '№' . $contract->certificate->number . ' (обладатель сертификата - ' . $contract->certificate->fio_child . ')',
            $headerText
        );
        $html = <<<EOD
<div style="font-size:12px;" > 
<p style="text-align: center;">Договор об образовании №$contract->number</p>
<br>
<div align="justify">$headerText о нижеследующем:</div>
</div>
EOD;

        if ($program->year > 1) {
            $chast = 'части';
        } else {
            $chast = '';
        }

        if ($program->year > 1) {
            $text5 = 'частью Программы';
        } else {
            $text5 = 'Программой';
        }

        if ($program->year >= 2) {
            $text144 = 'Полный срок реализации Программы - ' . $program->getCountMonths() . ' месяц(ев).';
        }


        if ($program->year == 1) {

            $month = (new \yii\db\Query())
                ->select(['month'])
                ->from('years')
                ->where(['id' => $contract->year_id])
                ->one();

            if ($month['month'] == 1) {
                $text144 = 'Полный срок реализации Программы - ' . $month['month'] . ' месяц.';
            }

            if ($month['month'] >= 2 and $month['month'] <= 4) {
                $text144 = 'Полный срок реализации Программы - ' . $month['month'] . ' месяцa.';
            }

            if ($month['month'] >= 5) {
                $text144 = 'Полный срок реализации Программы - ' . $month['month'] . ' месяцев.';
            }
        }


        if ($contract->sposob == 1) {
            $text77 = 'за наличный расчет';
        } else {
            $text77 = 'в безналичном порядке на счет Исполнителя, реквизиты которого указанны в разделе X настоящего Договора,';
        }


        if ($contract->other_m_price == 0) {
            $text88 = floor($contract->payer_first_month_payment) . ' руб. ' .
                round(($contract->payer_first_month_payment - floor($contract->payer_first_month_payment)) * 100, 0) . ' коп.';
            $text89 = floor($contract->parents_first_month_payment) . ' руб. ' .
                round(($contract->parents_first_month_payment - floor($contract->parents_first_month_payment)) * 100, 0) . ' коп.';
        } else {
            $text88 = floor($contract->payer_first_month_payment) . ' руб. ' .
                round(($contract->payer_first_month_payment - floor($contract->payer_first_month_payment)) * 100, 0) . ' коп. - за первый месяц периода обучения по Договору, ' .
                floor($contract->payer_other_month_payment) . ' руб. ' .
                round(($contract->payer_other_month_payment - floor($contract->payer_other_month_payment)) * 100, 0) . ' коп. - за каждый последующий месяц периода обучения по Договору.';
            $text89 = floor($contract->parents_first_month_payment) . ' руб. ' .
                round(($contract->parents_first_month_payment - floor($contract->parents_first_month_payment)) * 100, 0) . ' коп. - за первый месяц периода обучения по Договору, ' .
                floor($contract->parents_other_month_payment) . ' руб. ' .
                round(($contract->parents_other_month_payment - floor($contract->parents_other_month_payment)) * 100, 0) . ' коп. - за каждый последующий месяц периода обучения по Договору.';
        }

        $directivity = FormattingHelper::directivityForm($program->directivity);

        if ($contract->all_parents_funds > 0) {
            $text1 = ', а также оплатить часть образовательной услуги в объеме и на условиях, предусмотренных разделом V настоящего Договора ';

            $text3 = '4.2.1. Своевременно вносить плату за образовательную услугу в размере и порядке, определенных настоящим Договором, а также предоставлять платежные документы, подтверждающие такую оплату.<br>
             4.2.2. Создавать условия для получения Обучающимся образовательной услуги.<br>';

            $text4 = '5.1. Полная стоимость образовательной услуги за период обучения по Договору составляет ' . floor($contract->all_funds) . ' руб. 
    ' . round(($contract->all_funds - floor($contract->all_funds)) * 100, 0) . ' коп., в том числе:<br>
                5.1.1. Будет оплачено за счет средств сертификата дополнительного образования Обучающегося - ' . floor($contract->funds_cert) . ' руб. ' . round(($contract->funds_cert - floor($contract->funds_cert)) * 100, 0) . ' коп.<br>
                5.1.2. Будет оплачено за счет средств Заказчика - ' . floor($contract->all_parents_funds) . ' руб. ' . round(($contract->all_parents_funds - floor($contract->all_parents_funds)) * 100, 0) . ' коп.<br />
            5.2. Оплата за счет средств сертификата осуществляется в рамках договора ' . (Yii::$app->operator->identity->settings->document_name === Cooperate::DOCUMENT_NAME_FIRST ? 'о возмещении затрат' : 'об оплате дополнительного образования') . ' № ' . $cooperate['number'] . ' от ' . $date_cooperate[2] . '.' . $date_cooperate[1] . '.' . $date_cooperate[0] . ', заключенного между Исполнителем и ' . $payer->name_dat . ' (далее – Соглашение, Уполномоченная организация) ежемесячно не позднее 10-го числа месяца, следующего за месяцем оплаты в размере: ' . $text88 . '<br>
            5.3. Заказчик осуществляет оплату ежемесячно ' . $text77 . ' не позднее 10-го числа месяца, следующего за месяцем оплаты в размере: ' . $text89 . '<br>';

            if ($contract->payment_order === 1) {
                $text4 .= '5.4. Оплата за счет средств сертификата и Заказчика за месяц периода обучения по Договору осуществляется в полном объеме при условии, если по состоянию на первое число соответствующего месяца действие настоящего Договора не прекращено, независимо от фактического посещения Обучающимся занятий, предусмотренных учебным планом Программы в соответствующем месяце.<br>';
                $text4 .= '5.5. В случае отмены со стороны Исполнителя проведения одного или нескольких занятий в рамках оказания образовательной услуги объем оплаты по договору за месяц, в котором указанные занятия должны были быть проведены, уменьшается пропорционально доле таких занятий в общей продолжительности занятий в указанном месяце.<br>';
            } else {
                $text4 .= '5.4. Оплата за счет средств сертификата за месяц периода обучения по Договору осуществляется в полном объеме при условии, если по состоянию на первое число соответствующего месяца действие настоящего Договора не прекращено, независимо от фактического посещения Обучающимся занятий, предусмотренных учебным планом Программы в соответствующем месяце.<br>';
                $text4 .= '5.5. Оплата за счет средств Заказчика за месяц периода обучения по Договору осуществляется пропорционально фактическому посещению  Обучающимся занятий, предусмотренных учебным планом Программы в соответствующем месяце.<br>';
                $text4 .= '5.6. В случае отмены со стороны Исполнителя проведения одного или нескольких занятий в рамках оказания образовательной услуги объем оплаты по договору за месяц, в котором указанные занятия должны были быть проведены, уменьшается пропорционально доле таких занятий в общей продолжительности занятий в указанном месяце.<br>';
            }
        } else {
            $text1 = '';
            $text3 = '4.2.1. Создавать условия для получения Обучающимся образовательной услуги.<br>';
            $text4 = '5.1. Полная стоимость образовательной услуги за период обучения по Договору составляет ' . floor($contract->all_funds) . ' руб. ' . round(($contract->all_funds - floor($contract->all_funds)) * 100, 0) . ' коп.. Вся сумма будет оплачена за счет средств сертификата дополнительного образования Обучающегося.<br>         
            5.2. Оплата за счет средств сертификата осуществляется в рамках договора ' . (Yii::$app->operator->identity->settings->document_name === Cooperate::DOCUMENT_NAME_FIRST ? 'о возмещении затрат' : 'об оплате дополнительного образования') . ' № ' . $cooperate['number'] . ' от ' . $date_cooperate[2] . '.' . $date_cooperate[1] . '.' . $date_cooperate[0] . ', заключенного между Исполнителем и ' . $payer->name_dat . ' (далее – Соглашение, Уполномоченная организация) ежемесячно не позднее 10-го числа месяца, следующего за месяцем оплаты в размере: ' . $text88 . '<br>
            5.3. Оплата за счет средств сертификата за месяц периода обучения по Договору осуществляется в полном объеме при условии, если по состоянию на первое число соответствующего месяца действие настоящего Договора не прекращено, независимо от фактического посещения Обучающимся занятий, предусмотренных учебным планом Программы в соответствующем месяце.<br>';
            $text4 .= '5.4. В случае отмены со стороны Исполнителя проведения одного или нескольких занятий в рамках оказания образовательной услуги объем оплаты по договору за месяц, в котором указанные занятия должны были быть проведены, уменьшается пропорционально доле таких занятий в общей продолжительности занятий в указанном месяце.<br>';
        }


        if ($year->kvdop == 0 and $year->hoursindivid == 0) {
            $text2 = '
                4.1.5.2. Обеспечить при оказании образовательной услуги соблюдение следующих норм оснащения образовательного процесса средствами обучения и интенсивности их использования:<br>
                ' . $program->norm_providing . '<br>
                4.1.5.3. Обеспечить проведение занятий в группе с наполняемостью не более ' . $year->maxchild . ' детей.<br>
                4.1.5.4. Сохранить место за Обучающимся в случае пропуска занятий по уважительным причинам (с учетом своевременной оплаты образовательной услуги).<br>
                4.1.5.5. Обеспечить Обучающемуся уважение человеческого достоинства, защиту от всех форм физического и психического насилия, оскорбления личности, охрану жизни и здоровья.<br>
                ';
            if ($contract->cert_dol != 0) {
                $text2 .= '3.1.5.6. Принимать от Заказчика плату за образовательные услуги.<br>';
            }
        }


        if ($year->kvdop == 0 and $year->hoursindivid != 0) {
            $text2 = '
                4.1.5.2. Обеспечить индивидуальное консультирование обучающегося в рамках оказания образовательной услуги в объеме не менее ' . $year->hoursindivid . ' ак. час.<br>
                4.1.5.3. Обеспечить при оказании образовательной услуги соблюдение следующих норм оснащения образовательного процесса средствами обучения и интенсивности их использования:<br>
                ' . $program->norm_providing . '<br>
                4.1.5.4. Обеспечить проведение занятий в группе с наполняемостью не более ' . $year->maxchild . ' детей.<br>
                4.1.5.5. Сохранить место за Обучающимся в случае пропуска занятий по уважительным причинам (с учетом своевременной оплаты образовательной услуги).<br>
                4.1.5.6. Обеспечить Обучающемуся уважение человеческого достоинства, защиту от всех форм физического и психического насилия, оскорбления личности, охрану жизни и здоровья.<br>
                ';
            if ($contract->cert_dol != 0) {
                $text2 = $text2 . '4.1.5.7. Принимать от Заказчика плату за образовательные услуги.<br>';
            }
        }


        if ($year->kvdop != 0 and $year->hoursindivid == 0) {
            $text2 = '
                4.1.5.2. Обеспечить одновременное сопровождение группы детей не менее чем двумя педагогическими работниками, за счет привлечения к оказанию услуги дополнительного(ых) педагогического(их) работника(ов), квалификация которого(ых) соответствует следующим условиям:<br>
                ' . $year->kvdop . '<br>
                4.1.5.3. Обеспечить при оказании образовательной услуги соблюдение следующих норм оснащения образовательного процесса средствами обучения и интенсивности их использования:<br>
                ' . $program->norm_providing . '<br>
                4.1.5.4. Обеспечить проведение занятий в группе с наполняемостью не более ' . $year->maxchild . ' детей.<br>
                4.1.5.5. Сохранить место за Обучающимся в случае пропуска занятий по уважительным причинам (с учетом своевременной оплаты образовательной услуги).<br>
                4.1.5.6. Обеспечить Обучающемуся уважение человеческого достоинства, защиту от всех форм физического и психического насилия, оскорбления личности, охрану жизни и здоровья.<br>
                ';
            if ($contract->cert_dol != 0) {
                $text2 = $text2 . '4.1.5.7. Принимать от Заказчика плату за образовательные услуги.<br>';
            }
        }

        if ($year->kvdop != 0 and $year->hoursindivid != 0) {
            $text2 = '
                4.1.5.2. Обеспечить индивидуальное консультирование обучающегося в рамках оказания образовательной услуги в объеме не менее ' . $year->hoursindivid . ' ак. час.<br>
                4.1.5.3. Обеспечить одновременное сопровождение группы детей не менее чем двумя педагогическими работниками, за счет привлечения к оказанию услуги дополнительного(ых) педагогического(их) работника(ов), квалификация которого(ых) соответствует следующим условиям:<br>
                ' . $year->kvdop . '<br>
                4.1.5.4. Обеспечить при оказании образовательной услуги соблюдение следующих норм оснащения образовательного процесса средствами обучения и интенсивности их использования:<br> 
                «' . $program->norm_providing . '»<br>
                4.1.5.5. Обеспечить проведение занятий в группе с наполняемостью не более ' . $year->maxchild . ' детей.<br>
                4.1.5.6. Сохранить место за Обучающимся в случае пропуска занятий по уважительным причинам (с учетом своевременной оплаты образовательной услуги).<br>
                4.1.5.7. Обеспечить Обучающемуся уважение человеческого достоинства, защиту от всех форм физического и психического насилия, оскорбления личности, охрану жизни и здоровья.<br>
                ';
            if ($contract->cert_dol != 0) {
                $text2 = $text2 . '4.1.5.8. Принимать от Заказчика плату за образовательные услуги.<br>';
            }
        }

        $finishStudyDate = $contract->period == Contracts::CURRENT_REALIZATION_PERIOD ? Yii::$app->operator->identity->settings->current_program_date_to : Yii::$app->operator->identity->settings->future_program_date_to;

        if ($finishStudyDate > $group->datestop) {
            $finishStudyDate = $group->datestop;
        }

        if ($parentContract = $contract->getParent()) {
            $autoProlongationParagraph1 = '
1.1. Настоящий договор является официальным предложением (офертой) Исполнителя Заказчику к заключению договора на оказание платной образовательной услуги, указанной в разделе II настоящего Договора, содержит все существенные условия договора на оказание платных образовательных услуг по образовательным программам дополнительного образования и публикуется в глобальной компьютерной сети Интернет на сайте: http://pfdo.ru (далее – Сайт).<br>
1.2. Правовой основой регулирования отношений между Сторонами, возникших в силу заключения настоящего Договора, являются следующие нормативные документы: Гражданский кодекс Российской Федерации, Федеральный закон «Об образовании в Российской Федерации» от 29 декабря 2012 года №273-ФЗ, Правила оказания платных образовательных услуг, утвержденные постановлением Правительства РФ от 15 августа 2013 года №706.<br>
1.3. Безусловное принятие (акцепт) условий настоящего Договора со стороны Заказчика в соответствии со ст. 438 ГК РФ, осуществляется на основании заранее данного Заказчиком при принятии (акцепте) договора от ' . \Yii::$app->formatter->asDate($parentContract->date) . ' № ' . $parentContract->number . ', заключенного между Заказчиком и Исполнителем, согласия на заключение иных договоров-оферт, предусматривающих оказание Обучающемуся образовательных услуг по реализации частей образовательной программы, указанной в разделе II настоящего Договора.<br>
1.3.1. По требованию Исполнителя Заказчик обязан подтвердить способом безусловное принятие (акцепта) условий настоящего Договора посредством предоставления заявления на оказание услуги по дополнительной образовательной программе, в рамках образовательной услуги, указанной в разделе II настоящего Договора.<br>
1.4. Осуществляя акцепт настоящего Договора в порядке, определенном пунктом 1.3 Договора-оферты, Заказчик соглашается, полностью и безоговорочно принимает все условия настоящего Договора в том виде, в каком они изложены в тексте настоящего Договора.<br>
1.5. Заказчик вправе отказаться от принятия (акцепта) условий настоящего Договора посредством направления Исполнителю уведомления об отказе от акцепта Договора после его получения.<br>
1.6. Настоящий Договор может быть отозван Исполнителем до момента получения акцепта со стороны Заказчика.<br>
1.7. Настоящий Договор не требует скрепления печатями и/или подписания Заказчиком и Исполнителем, сохраняя при этом полную юридическую силу.<br>';

            $autoProlongationParagraph9 = '9.6. Для целей осуществления взаимодействия между Сторонами, в том числе связанных с направлением официальных уведомлений о расторжения настоящего Договора, отказе от акцепта Договора, Стороны договорились использовать личные кабинеты Сайта.<br>';

            $autoProlongationParagraph10 = 'Сведения о Заказчике и Обучающемся указываются в заявлении на зачисление Обучающегося на обучение по дополнительной образовательной программе, указанной в разделе II настоящего Договора, предоставленном Заказчиком при принятии (акцепте) договора от ' . \Yii::$app->formatter->asDate($parentContract->date) . ' № ' . $parentContract->number . ', являющемся неотъемлемой частью настоящего договора.';
        } else {
            $autoProlongationParagraph1 = '1.1. Настоящий договор является официальным предложением (офертой) Исполнителя Заказчику к заключению договора на оказание платной образовательной услуги, указанной в разделе II настоящего Договора, содержит все существенные условия договора на оказание платных образовательных услуг по образовательным программам дополнительного образования и публикуется в глобальной компьютерной сети Интернет на сайте: http://pfdo.ru (далее – Сайт). <br>
            1.2. Правовой основой регулирования отношений между Сторонами, возникших в силу заключения настоящего Договора, являются следующие нормативные документы: Гражданский кодекс Российской Федерации, Федеральный закон «Об образовании в Российской Федерации» от 29 декабря 2012 года №273-ФЗ, Правила оказания платных образовательных услуг, утвержденные постановлением Правительства РФ от 15 августа 2013 года №706.<br>
            1.3. В качестве необходимого и достаточного действия, определяющего безусловное принятие (акцепт) условий Договора со стороны Заказчика в соответствии со ст. 438 ГК РФ, определяется подписание Заказчиком заявления о зачислении Обучающегося на обучение по дополнительной образовательной программе, в рамках образовательной услуги, указанной в разделе II настоящего Договора.<br>
            1.4. Заявление о зачислении на Обучающегося на обучение по дополнительной образовательной программе, указанное в пункте 1.3 настоящего Договора, является неотъемлемой частью настоящего Договора и должно содержать указание на принятие Заказчиком условий настоящего Договора, а также следующие предусмотренные Правилами оказания платных образовательных услуг сведения:<br>
                а) фамилия, имя, отчество (при наличии) Заказчика, телефон заказчика;<br>
                б) место жительства Заказчика;<br>
                в) фамилия, имя, отчество (при наличии) Обучающегося, его место жительства, телефон.<br>
            1.5. Совершая действия по акцепту настоящего Договора Заказчик гарантирует, что он имеет законные права вступать в договорные отношения с Исполнителем. <br>
            1.6. Осуществляя акцепт настоящего Договора в порядке, определенном пунктом 1.3 Договора-оферты, Заказчик гарантирует, что ознакомлен, соглашается, полностью и безоговорочно принимает все условия настоящего Договора в том виде, в каком они изложены в тексте настоящего Договора. <br>
            1.7. Настоящий Договор может быть отозван Исполнителем до момента получения акцепта со стороны Заказчика.<br>
            1.8. Настоящий Договор не требует скрепления печатями и/или подписания Заказчиком и Исполнителем, сохраняя при этом полную юридическую силу.<br>';

            $autoProlongationParagraph9 = '9.6. В случае если образовательная услуга, оказываемая по настоящему Договору, предусматривает реализацию части Программы, принятие (акцепт) Заказчиком условий настоящего Договора, предусматривает предоставление его заранее данного согласия на заключение иных договоров-оферт, сформированных в соответствии с требованиями, указанными в пунктах 9.7 – 9.8 настоящего Договора, предусматривающих оказание Обучающемуся образовательных услуг по реализации иных частей Программы, не освоенных до момента заключения настоящего Договора.<br>
9.7. При формировании предложения (оферты) Исполнителем Заказчику к заключению договора-оферты, предусмотренного пунктом 9.6 настоящего Договора, Исполнитель информирует Заказчика о наличии и условиях предложения (оферты) не позднее чем за 5 рабочих дней до начала обучения в соответствии с договором-офертой.<br>
9.8. Условия, предусматриваемые предложением (офертой) Исполнителем Заказчику к заключению договора-оферты, предусмотренного пунктом 9.6 настоящего Договора, не должны отличаться от условий обучения по предусмотренной им части Программы, действующими на момент заключения настоящего Договора. Увеличение стоимости услуги относительно стоимости, установленной на момент заключения настоящего Договора не допускается, за исключением увеличения стоимости указанных услуг с учетом уровня инфляции, предусмотренного основными характеристиками федерального бюджета на очередной финансовый год и плановый период.<br>
9.8. Заказчик вправе отозвать заранее данное согласие на заключение иных договоров-оферт, предусмотренных пунктом 9.7 Договора, посредством направления Исполнителю уведомления в простой письменной форме, либо отклонения оферты в личном кабинете Сайта. Заказчик самостоятельно отслеживает выставление договоров-оферт, предусмотренных пунктом 9.7 Договора, в личном кабинете Сайта.<br>
9.9. Для целей осуществления взаимодействия между Сторонами, в том числе связанных с направлением официальных уведомлений о расторжения настоящего Договора, Стороны договорились использовать личные кабинеты Сайта.<br>';

            $autoProlongationParagraph10 = 'Сведения о Заказчике и Обучающемся указываются в заявлении на зачисление Обучающегося на обучение по дополнительной образовательной программе, указанном в пункте 1.3 настоящего Договора, являющемся неотъемлемой частью настоящего Договора.';
        }

        $text = '
        <div style="font-size: ' . $contract->fontsize . '" >
        <p style="text-align:center">I. Общие положения и правовое основание Договора-оферты</p>
        
        <div align="justify">'
            . $autoProlongationParagraph1 .
            '</div>
        
        
        <p style="text-align:center">II. Предмет Договора</p>

<div align="justify">
	2.1. Исполнитель обязуется оказать Обучающемуся образовательную услугу по реализации ' . $chast . ' дополнительной общеобразовательной программы ' . $directivity . ' направленности «' . $program->name . '» ' . ((null === $contract->module->name) ? ('модуля (года) - ' . $contract->module->year) : 'модуля: «' . $contract->module->name . '»') . ' (далее – Образовательная услуга, Программа), в пределах учебного плана программы, предусмотренного на период обучения по Договору.<br>
    2.2. Форма обучения и используемые образовательные технологии: ' . $programform . '<br>
	2.3. Заказчик обязуется содействовать получению Обучающимся образовательной услуги' . $text1 . '.<br>
	2.4. ' . $text144 . ' Период обучения по Договору: с ' . Yii::$app->formatter->asDate($contract->start_edu_contract) . ' по ' . Yii::$app->formatter->asDate($finishStudyDate) . '.
</div>

<p style="text-align:center">III. Права Исполнителя, Заказчика и Обучающегося</p>

<div align="justify">
    3.1.  Исполнитель вправе:<br>
    3.1.1. Самостоятельно осуществлять образовательный процесс, устанавливать системы оценок, формы, порядок и периодичность проведения промежуточной аттестации Обучающегося.<br>
    3.1.2. Применять к Обучающемуся меры поощрения и меры дисциплинарного взыскания в соответствии с законодательством Российской Федерации, учредительными документами Исполнителя, настоящим Договором и локальными нормативными актами Исполнителя.<br>
    3.1.3. В случае невозможности проведения необходимого числа занятий, предусмотренных учебным планом, на определенный месяц оказания образовательной услуги, обеспечить оказание образовательной услуги в полном объеме за счет проведения дополнительных занятий в последующие месяцы действия настоящего Договора.<br>
    3.2. Заказчик вправе:<br>
    3.2.1. Получать информацию от Исполнителя по вопросам организации и обеспечения надлежащего оказания образовательной услуги.<br>
    3.2.2. Обращаться к Исполнителю по вопросам, касающимся образовательного процесса.<br>
    3.2.3. Участвовать в оценке качества образовательной услуги, проводимой в рамках системы персонифицированного финансирования.<br>
    3.3. Обучающемуся предоставляются академические права в соответствии с частью 1 статьи 34 Федерального закона от 29 декабря 2012 г. №273-ФЗ "Об образовании в Российской Федерации". Обучающийся также вправе:<br>
    3.3.1. Получать информацию от Исполнителя по вопросам организации и обеспечения надлежащего оказания образовательной услуги.<br>
    3.3.2. Обращаться к Исполнителю по вопросам, касающимся образовательного процесса.<br>
    3.3.3. Пользоваться в порядке, установленном локальными нормативными актами, имуществом Исполнителя, необходимым для освоения Программы.<br>
    3.3.4. Принимать в порядке, установленном локальными нормативными актами, участие в социально-культурных, оздоровительных и иных мероприятиях, организованных Исполнителем.<br>
    3.3.5. Получать полную и достоверную информацию об оценке своих знаний, умений, навыков и компетенций, а также о критериях этой оценки.
</div>

<p style="text-align:center">IV. Обязанности Исполнителя, Заказчика и Обучающегося</p>

<div align="justify">
	4.1. Исполнитель обязан:<br>
    4.1.1. Зачислить Обучающегося в качестве учащегося на обучение по Программе (в случае если Обучающийся не зачислен в качестве учащегося по Программе на момент заключения настоящего Договора).<br>
    4.1.2. Довести до Заказчика информацию, содержащую сведения о предоставлении платных образовательных услуг в порядке и объеме, которые предусмотрены Законом Российской Федерации "О защите прав потребителей" и Федеральным законом "Об образовании в Российской Федерации"<br>
    4.1.3. Организовать и обеспечить надлежащее предоставление образовательных услуг, предусмотренных разделом I настоящего Договора. Образовательные услуги оказываются в соответствии с учебным планом Программы и расписанием занятий Исполнителя.<br>
    4.1.4. Обеспечить полное выполнение учебного плана Программы, предусмотренного на период обучения по Договору. В случае отмены проведения части занятий, предусмотренных в учебном плане на конкретный месяц, провести их дополнительно в том же или последующем месяце, либо провести перерасчет стоимости оплаты за месяц, предусмотренный разделом V настоящего Договора.<br>
    4.1.5. Обеспечить Обучающемуся предусмотренные Программой условия ее освоения, в том числе:<br>
        4.1.5.1. Обеспечить сопровождение оказания услуги педагогическим работником, квалификация которого соответствует следующим условиям:<br> «' . $year->kvfirst . '»<br>
        ' . $text2 . '
        
    4.2. Заказчик обязан:<br>
        ' . $text3 . '
        
    4.3. Обучающийся обязан:<br>
        4.3.1. Выполнять задания для подготовки к занятиям, предусмотренным учебным планом Программы<br>
        4.3.2. Извещать Исполнителя о причинах отсутствия на занятиях.<br>
        4.3.3. Обучаться по образовательной программе с соблюдением требований, установленных учебным планом Программы<br>
        4.3.4. Соблюдать требования учредительных документов, правила внутреннего распорядка и иные локальные нормативные акты Исполнителя.<br>
        4.3.5. Соблюдать иные требования, установленные в статье 43 Федерального закона от 29 декабря 2012 г. №273-ФЗ "Об образовании в Российской Федерации"<br>
</div>

<p style="text-align:center">V. Стоимость услуги, сроки и порядок их оплаты</p>
</div>
';

        $mpdf = new mPDF();
        $mpdf->WriteHtml($html); // call mpdf write html
        $mpdf->WriteHtml($text); // call mpdf write html

        $mpdf->WriteHtml('<div align="justify"  style="font-size: ' . $contract->fontsize . '">' . $text4 . '</div>');

        $correspondentInvoice = (!empty($organization->correspondent_invoice)) ? '<p>Корреспондентский счёт (к/с): ' . $organization->correspondent_invoice . '</p>' : '';

        $mpdf->WriteHtml('
<div style="font-size: ' . $contract->fontsize . '" >
<p style="text-align:center">VI. Основания изменения и порядок расторжения договора</p>

<div align="justify">
    6.1. Условия, на которых заключен настоящий Договор, могут быть изменены по соглашению Сторон или в соответствии с законодательством Российской Федерации.<br>
    6.2. Настоящий Договор может быть расторгнут по соглашению Сторон.<br>
    6.3. Настоящий Договор может быть расторгнут по инициативе Исполнителя в одностороннем порядке в случаях:<br>
    установления нарушения порядка приема Обучающегося на обучение по Программе, повлекшего по вине Обучающегося его незаконное зачисление на обучение по Программе;<br>
    просрочки оплаты стоимости образовательной услуг со стороны Уполномоченной организации и/или Заказчика.
    невозможности надлежащего исполнения обязательства по оказанию образовательной услуги вследствие действий (бездействия) Обучающегося;<br>
    приостановления действия сертификата дополнительного образования Обучающегося;<br>
    получения предписания о расторжении договора от Уполномоченной организации, направляемой Уполномоченной организацией Исполнителю в соответствии с Соглашением;<br>
    в иных случаях, предусмотренных законодательством Российской Федерации.<br>
    6.4. Настоящий Договор может быть расторгнут по инициативе Заказчика.<br>
    6.5. Исполнитель вправе отказаться от исполнения обязательств по Договору при условии полного возмещения Заказчику убытков.<br>
    6.6. Заказчик вправе отказаться от исполнения настоящего Договора при условии оплаты Исполнителю фактически понесенных им расходов, связанных с исполнением обязательств по Договору.<br>
    6.7. Для расторжения договора Заказчик направляет Исполнителю уведомление о расторжении настоящего Договора. Датой расторжения договора является последний день месяца, в котором было направлено указанное уведомление о расторжении настоящего Договора.<br>
    6.8. Для расторжения договора Исполнитель направляет Заказчику уведомление о расторжении настоящего Договора, в котором указывает причину расторжения договора. Датой расторжения договора является последний день месяца, в котором было направлено указанное уведомление о расторжении настоящего Договора.<br>
</div>

<p style="text-align:center">VII. Ответственность Исполнителя, Заказчика и Обучающегося</p>

<div align="justify">
    7.1. За неисполнение или ненадлежащее исполнение своих обязательств по Договору Стороны несут ответственность, предусмотренную законодательством Российской Федерации и Договором.<br>
    7.2. При обнаружении недостатка образовательной услуги, в том числе оказания ее не в полном объеме, предусмотренном ' . $text5 . ', Заказчик вправе по своему выбору потребовать:<br>
    7.2.1. Безвозмездного оказания образовательной услуги.<br>
    7.2.2. Возмещения понесенных им расходов по устранению недостатков оказанной образовательной услуги своими силами или третьими лицами.<br>
    7.3. Заказчик вправе отказаться от исполнения Договора и потребовать полного возмещения убытков, если в срок недостатки образовательной услуги не устранены Исполнителем. Заказчик также вправе отказаться от исполнения Договора, если им обнаружен существенный недостаток оказанной образовательной услуги или иные существенные отступления от условий Договора.<br>
    7.4. Если Исполнитель нарушил сроки оказания образовательной услуги (сроки начала и (или) окончания оказания образовательной услуги и (или) промежуточные сроки оказания образовательной услуги) либо если во время оказания образовательной услуги стало очевидным, что она не будет осуществлена в срок, Заказчик вправе по своему выбору:<br>
    7.4.1. Назначить Исполнителю новый срок, в течение которого Исполнитель должен приступить к оказанию образовательной услуги и (или) закончить оказание образовательной услуги.<br>
    7.4.2. Поручить оказать образовательную услугу третьим лицам за разумную цену и потребовать от Исполнителя возмещения понесенных расходов.<br>
    7.4.3. Расторгнуть Договор.<br>
    7.5. Заказчик вправе потребовать полного возмещения убытков, причиненных ему в связи с нарушением сроков начала и (или) окончания оказания образовательной услуги, а также в связи с недостатками образовательной услуги.<br>
</div>

<p style="text-align:center">VIII. Срок действия Договора</p>

<div align="justify">
    8.1. Настоящий Договор вступает в силу с ' . $date_elements_user[2] . '.' . $date_elements_user[1] . '.' . $date_elements_user[0] . ' и действует до полного исполнения Сторонами своих обязательств.<br>
</div>

<p style="text-align:center">IX. Заключительные положения</p>

<div align="justify">
    9.1. Сведения,  указанные  в  настоящем  Договоре,    соответствуют информации,  размещенной  на  официальном  сайте  Исполнителя    в   сети "Интернет" на дату заключения настоящего Договора.<br>
    9.2. Под периодом обучения по Договору  понимается  промежуток  времени  с  даты проведения первого занятия по дату проведения последнего занятия в рамках оказания образовательной услуги.<br>
    9.3. Настоящий Договор составлен в простой письменной форме в электронном виде и размещен на Сайте с обеспечение доступа к нему Заказчика и Исполнителя.  Изменения и дополнения настоящего Договора могут производиться только посредством формирования дополнительных оферт со стороны Заказчика и их акцепта со стороны Исполнителя.<br>
    9.4. Изменения Договора оформляются дополнительными соглашениями к Договору.<br>
    9.5. Изменения раздела IV настоящего договора допускаются лишь при условии согласования указанных изменений с Уполномоченной организацией.<br>'
            . $autoProlongationParagraph9 .
            '<p style="text-align:center">X. Адреса и реквизиты сторон</p>
</div>
<table align="center" <div style="font-size: ' . $contract->fontsize . '" > border="0" cellpadding="10" cellspacing="10">
	<tbody>
		<tr>
			<td width="300" style="vertical-align: top;">
            <p>Исполнитель</p>
            <br>
			<p>' . $organization->name . '</p>

			<p>Юридический адрес: ' . $organization->address_legal . '</p>

			<p>Адрес местонахождения: ' . $organization->address_actual . '</p>

			<p>Наименование банка: ' . $organization->bank_name . '</p>
            
            <p>Город банка: ' . $organization->bank_sity . '</p>

			<p>БИК: ' . $organization->bank_bik . '</p>

			<p>Лицевой счёт (л/с): ' . $organization->korr_invoice . '</p>

            ' . $correspondentInvoice . '

			<p>р/с: ' . $organization->rass_invoice . '</p>
			
			' . (empty($organization->receiver) ? '' : "<p>Получатель: " . $organization->receiver . "</p>") . '
            
            <p>ИНН: ' . $organization->inn . '</p>
            
            <p>КПП: ' . $organization->KPP . '</p>
            
            <p>ОРГН/ОРГНИП: ' . $organization->OGRN . '</p>
            
			</td>
			<td width="300"  style="vertical-align: top;">
                <p>Заказчик</p>
                <br>
                <p>' . $autoProlongationParagraph10 . '</p>
			</td>
		</tr>
	</tbody>
</table>
</div>');

        return $mpdf;
    }

    /**
     * @return string
     */
    private function getRealizationPeriod()
    {
        return $this->realizationPeriod;
    }

    /**
     * @param mixed $realizationPeriod
     */
    private function setRealizationPeriod($realizationPeriod)
    {
        $this->realizationPeriod = $realizationPeriod;
    }

    /**
     * установить значения начала обучения по контракту
     *
     * @param $startEduContract
     */
    public function setStartEduContract($startEduContract)
    {
        $this->start_edu_contract = $startEduContract;
    }

    /**
     * @return OperatorSettings
     */
    private function getOperatorSettings(): OperatorSettings
    {
        if (null === $this->operatorSettings) {
            $this->operatorSettings = Yii::$app->operator->identity->settings;
        }

        return $this->operatorSettings;
    }
}