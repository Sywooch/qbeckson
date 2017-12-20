<?php

namespace app\models\contracts;

use app\helpers\CalculationHelper;
use app\models\Certificates;
use app\models\Contracts;
use app\models\Groups;
use app\models\OperatorSettings;
use app\models\Payers;
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