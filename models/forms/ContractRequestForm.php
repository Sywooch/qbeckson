<?php

namespace app\models\forms;

use app\helpers\CalculationHelper;
use app\models\Certificates;
use app\models\Contracts;
use app\models\Groups;
use app\models\OperatorSettings;
use Yii;
use yii\base\Model;

/**
 * Class ContractRequestForm
 * @package app\models\forms
 */
class ContractRequestForm extends Model
{
    const CURRENT_REALIZATION_PERIOD = 'current';
    const FUTURE_REALIZATION_PERIOD = 'future';

    public $dateFrom;
    public $dateTo;

    private $group;
    private $settings;
    private $certificate;
    private $realizationPeriod;

    /**
     * ContractRequestForm constructor.
     * @param integer $groupId
     * @param array $config
     */
    public function __construct($groupId, $config = [])
    {
        $this->setGroup($groupId);
        parent::__construct($config);
    }

    /**
     * Когда попали в окно расчетов по договору:
     *
     * Дата начала по договору не может быть выбрать ранее, чем дата начала обучения в группе.
     * Дата окончания по договору одновременно не может быть больше даты окончания обучения в
     * группе и не может быть позже последнего числа текущего (или будущего) периода программы
     * Вариант А: заключаем договор на текущий период
     * Вариант Б: заключаем договор  на будущий период (Важно разделить действующие договоры
     * (подтвержденные со статусом 1) и договоры уже заключенные, но начало действия которых еще не наступило
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['dateFrom'], 'required'],
            [['dateFrom'], 'date', 'format' => 'php:Y-m-d'],
            [['dateFrom'], 'validateDate'],
        ];
    }

    /**
     * Валидация даты начала и конца.
     *
     * @param $attribute
     */
    public function validateDate($attribute)
    {
        $group = $this->getGroup();
        if (strtotime($this->$attribute) < strtotime($group->datestart) ||
            strtotime($this->$attribute) > strtotime($group->datestop)) {
            $this->addError(
                $attribute,
                'Дата должна быть в пределах: '. $group->datestart .' ' . $group->datestop . '.'
            );

            return;
        }
        if (null === ($settings = $this->getSettings())) {
            $this->addError($attribute, 'Должны быть указаны периоды реализации программ в настройках.');

            return;
        }

        if (strtotime($this->dateFrom) >= strtotime($settings->current_program_date_from) &&
            strtotime($this->dateFrom) <= strtotime($settings->current_program_date_to)
        ) {
            $this->setRealizationPeriod(self::CURRENT_REALIZATION_PERIOD);
            $this->dateTo = $settings->current_program_date_to;
            if (strtotime($group->datestop) < strtotime($settings->current_program_date_to)) {
                $this->dateTo = $group->datestop;
            }
        }

        if (strtotime($this->dateFrom) >= strtotime($settings->future_program_date_from) &&
            strtotime($this->dateFrom) <= strtotime($settings->future_program_date_to)
        ) {
            $this->setRealizationPeriod(self::FUTURE_REALIZATION_PERIOD);
            $this->dateTo = $settings->future_program_date_to;
            if (strtotime($group->datestop) < strtotime($settings->future_program_date_to)) {
                $this->dateTo = $group->datestop;
            }
        }

        if (null === $this->getRealizationPeriod()) {
            $this->addError($attribute, 'В данный период времени реализация программ не выполняется.');

            return;
        }

        if ($this->dateFrom === $this->dateTo) {
            $this->addError($attribute, 'Даты должны отличаться');

            return;
        }
    }

    /**
     * Порядок произведения расчетов:
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
     * Вариант А: funds_cert = min (нормативная стоимость первого месяца + нормативная стоимость прочего месяца*число прочих месяцев; остаток сертификата текущего периода)
     * Вариант Б: funds_cert = min (нормативная стоимость первого месяца + нормативная стоимость прочего месяца*число прочих месяцев; остаток сертификата будущего периода)
     * all_parents_funds= All_funds - funds_cert
     * first_m_price – цена первого месяца
     * other_m_price – цена прочего месяца (0 – если всего один месяц)
     * first_m_nprice – нормативная стоимость первого месяца (0 – если всего один месяц) other_m_nprice - нормативная стоимость прочего месяца
     * Подтверждение заявки: вот тут вот будут расчеты влиять на остатки и резервы текущего и будущего периодов
     *
     * @return mixed
     */
    public function save()
    {
        if (null !== ($group = $this->getGroup()) && $this->validate()) {
            // Период реализации в месяцах
            $realizationPeriodInMonthes = CalculationHelper::monthesInPeriod($this->dateFrom, $this->dateTo);
            // Период реализации в днях
            $realizationPeriodInDays = CalculationHelper::daysBetweenDates($this->dateFrom, $this->dateTo);
            // Период реализации группы в днях
            $groupRealizationPeriod = CalculationHelper::daysBetweenDates($group->datestart, $group->datestop);
            // Всего необходимо заплатить
            $all_funds = CalculationHelper::roundTo(
                $realizationPeriodInDays / $groupRealizationPeriod * $group->module->price
            );
            // Нормативная стоимость
            $normativePrice = CalculationHelper::roundTo(
                $realizationPeriodInDays / $groupRealizationPeriod * $group->module->normative_price
            );
            // Меньшее из трёх all_funds / $normativePrice / отсток по сертификату
            $funds_cert = min($all_funds, $normativePrice, $this->getCertificate()->balance);
            // Сколько надо заплатить родителям
            $all_parents_funds = $all_funds - $funds_cert;
            // Доля сертификата
            $cert_dol = $all_parents_funds / $all_funds;
            // Доля плательщика
            $payer_dol = 1 - $cert_dol;
            //Если кто-во месяцев реализации > 1
            if ($realizationPeriodInMonthes > 1) {
                // Дней в первом месяце
                $daysInFirstMonth = CalculationHelper::daysBetweenDates(
                    $this->dateFrom,
                    date('Y-m-t', strtotime($this->dateFrom))
                );
                $otherMonthesPricePerMonth = CalculationHelper::roundTo(
                    ($realizationPeriodInDays - $daysInFirstMonth) /
                    $groupRealizationPeriod * $group->module->price / ($realizationPeriodInMonthes - 1),
                    CalculationHelper::TO_DOWN
                );
                $otherMonthesNormativePricePerMonth = CalculationHelper::roundTo(
                    ($realizationPeriodInDays - $daysInFirstMonth) /
                        $groupRealizationPeriod * $group->module->normative_price / ($realizationPeriodInMonthes - 1),
                    CalculationHelper::TO_DOWN
                );
                $firstMonthPrice = $all_funds - $otherMonthesPricePerMonth * ($realizationPeriodInMonthes - 1);
                $firstMonthNormativePrice = $normativePrice -
                    $otherMonthesNormativePricePerMonth * ($realizationPeriodInMonthes - 1);

                $parents_other_month_payment = CalculationHelper::roundTo(
                    ($realizationPeriodInDays - $daysInFirstMonth) /
                        $realizationPeriodInDays * $all_parents_funds / ($realizationPeriodInMonthes - 1),
                    CalculationHelper::TO_DOWN
                );
                $parents_first_month_payment = $all_parents_funds - $parents_other_month_payment *
                    ($realizationPeriodInMonthes - 1);

                $payer_other_month_payment = CalculationHelper::roundTo(
                    ($realizationPeriodInDays - $daysInFirstMonth) /
                    $realizationPeriodInDays * $funds_cert / ($realizationPeriodInMonthes - 1),
                    CalculationHelper::TO_DOWN
                );
                $payer_first_month_payment = $funds_cert - $payer_other_month_payment *
                    ($realizationPeriodInMonthes - 1);
            }

            $contract = [
                'certificate_id' => $this->getCertificate()->id,
                'payer_id' => $this->getCertificate()->payer_id,
                'group_id' => $group->id,
                'program_id' => $group->program_id,
                'year_id' => $group->year_id,
                'organization_id' => $group->organization_id,
                'start_edu_contract' => $this->dateFrom,
                'stop_edu_contract' => $this->dateTo,
                'prodolj_d' => $realizationPeriodInDays,
                'prodolj_m' => $realizationPeriodInMonthes,
                'prodolj_m_user' => $realizationPeriodInMonthes,
                'all_funds' => $all_funds,
                'funds_cert' => $funds_cert,
                'all_parents_funds' => $all_parents_funds,
                'cert_dol' => $cert_dol,
                'payer_dol' => $payer_dol,
                'first_m_price' => isset($firstMonthPrice) ? $firstMonthPrice : $all_funds,
                'first_m_nprice' => isset($firstMonthNormativePrice) ? $firstMonthNormativePrice : $normativePrice,
                'other_m_price' => isset($otherMonthesPricePerMonth) ? $otherMonthesPricePerMonth : 0,
                'other_m_nprice' => isset($otherMonthesNormativePricePerMonth) ? $otherMonthesNormativePricePerMonth : 0,
                'parents_first_month_payment' => isset($parents_first_month_payment) ? $parents_first_month_payment : $all_parents_funds,
                'parents_other_month_payment' => isset($parents_other_month_payment) ? $parents_other_month_payment : 0,
                'payer_first_month_payment' => isset($payer_first_month_payment) ? $payer_first_month_payment : $funds_cert,
                'payer_other_month_payment' => isset($payer_other_month_payment) ? $payer_other_month_payment : 0,
            ];

            return $contract;
        }

        return false;
    }

    /**
     * @return OperatorSettings
     */
    public function getSettings()
    {
        if (null === $this->settings) {
            $this->settings = Yii::$app->operator->identity->settings;
        }

        return $this->settings;
    }

    /**
     * @return Groups
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param integer $groupId
     * @throw \DomainException
     */
    public function setGroup($groupId)
    {
        $this->group = Groups::findOne($groupId);
        if (null === $this->group) {
            throw new \DomainException('Group not found');
        }
    }

    /**
     * @return mixed
     */
    public function getRealizationPeriod()
    {
        return $this->realizationPeriod;
    }

    /**
     * @param mixed $realizationPeriod
     */
    public function setRealizationPeriod($realizationPeriod)
    {
        $this->realizationPeriod = $realizationPeriod;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'dateFrom' => 'Дата начала',
            'dateTo' => 'Дата окончания'
        ];
    }

    /**
     * @return Certificates
     */
    public function getCertificate()
    {
        if (null === $this->certificate) {
            $this->certificate = Yii::$app->user->identity->certificate;
        }

        return $this->certificate;
    }
}
