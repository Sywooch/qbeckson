<?php

namespace app\models\forms;

use app\helpers\CalculationHelper;
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
            [['dateFrom', 'dateTo'], 'required'],
            [['dateFrom', 'dateTo'], 'date', 'format' => 'php:Y-m-d'],
            [['dateFrom', 'dateTo'], 'validateDate'],
            [['dateFrom'], 'validateDateFrom'],
            [['dateTo'], 'validateDateTo'],
            [['dateFrom', 'dateTo'], 'validateRealizationPeriod'],
        ];
    }

    /**
     * Валидация периода реализации программы.
     *
     * @param $attribute
     */
    public function validateRealizationPeriod($attribute)
    {
        if (null === ($settings = $this->getSettings())) {
            $this->addError($attribute, 'Должны быть указаны периоды реализации программ в настройках.');
        }
        if (strtotime($this->dateFrom) >= strtotime($settings->current_program_date_from) &&
            strtotime($this->dateFrom) <= strtotime($settings->current_program_date_to)
        ) {
            $this->setRealizationPeriod(self::CURRENT_REALIZATION_PERIOD);
            if (strtotime($this->dateTo) > strtotime($settings->current_program_date_to)) {
                $this->addError(
                    'dateTo',
                    'Дата должна быть в пределах: '.
                        $settings->current_program_date_from .' ' . $settings->current_program_date_to . '.'
                );
            }
        }
        if (strtotime($this->dateFrom) >= strtotime($settings->future_program_date_from) &&
            strtotime($this->dateFrom) <= strtotime($settings->future_program_date_to)
        ) {
            $this->setRealizationPeriod(self::FUTURE_REALIZATION_PERIOD);
            if (strtotime($this->dateTo) > strtotime($settings->future_program_date_to)) {
                $this->addError(
                    'dateTo',
                    'Дата должна быть в пределах: '.
                    $settings->future_program_date_from .' ' . $settings->future_program_date_to . '.'
                );
            }
        }
        if (null === $this->getRealizationPeriod()) {
            $this->addError($attribute, 'В данный период времени реализация программ не выполняется.');
        }
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
        }
        if ($this->dateFrom === $this->dateTo) {
            $this->addError($attribute, 'Даты должны отличаться');
        }
    }

    /**
     * @param $attribute
     */
    public function validateDateFrom($attribute)
    {
        if ($this->dateTo && strtotime($this->$attribute) > strtotime($this->dateTo)) {
            $this->addError($attribute, 'Дата начала должна быть меньше даты окончания.');
        }
    }

    /**
     * @param $attribute
     */
    public function validateDateTo($attribute)
    {
        if ($this->dateFrom && strtotime($this->$attribute) < strtotime($this->dateFrom)) {
            $this->addError($attribute, 'Дата окончания должна быть больше даты начала.');
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
     * @return array
     */
    public function save()
    {
        if (null !== ($group = $this->getGroup()) && $this->validate()) {
            $realizationPeriodInMonthes = CalculationHelper::monthesInPeriod($this->dateFrom, $this->dateTo);
            $realizationPeriodInDays = CalculationHelper::daysBetweenDates($this->dateFrom, $this->dateTo);
            $groupRealizationPeriod = CalculationHelper::daysBetweenDates($group->datestart, $group->datestop);

            $normativePrice = CalculationHelper::roundTo(
                $realizationPeriodInDays / $groupRealizationPeriod * $group->module->normative_price
            );
            $price = CalculationHelper::roundTo(
                $realizationPeriodInDays / $groupRealizationPeriod * $group->module->price
            );

            if ($realizationPeriodInMonthes === 1) {

            } else {
                $daysInFirstMonth = CalculationHelper::daysBetweenDates(
                    $this->dateFrom,
                    date('Y-m-t', strtotime($this->dateFrom))
                );
                $firstMonthNormativePrice = CalculationHelper::roundTo(
                    $daysInFirstMonth / $groupRealizationPeriod * $group->module->normative_price,
                    CalculationHelper::TO_DOWN
                );
                $firstMonthPrice = CalculationHelper::roundTo(
                    $daysInFirstMonth / $groupRealizationPeriod * $group->module->price,
                    CalculationHelper::TO_DOWN
                );

                $otherMonthesNormativePrice = ($realizationPeriodInDays - $daysInFirstMonth) /
                    $groupRealizationPeriod * $group->module->normative_price / ($realizationPeriodInMonthes - 1);
                $otherMonthesPrice = ($realizationPeriodInDays - $daysInFirstMonth) /
                    $groupRealizationPeriod * $group->module->price / ($realizationPeriodInMonthes - 1);



                //$otherMonthesNormativePrice = CalculationHelper::roundTo($otherMonthesNormativePrice);
            }

            /*$contract = new Contracts([
                'certificate_id' => Yii::$app->user->identity->certificate->id,
                'payer_id' => Yii::$app->user->identity->certificate->payer_id,
                'group_id' => $group->id,
                'program_id' => $group->program_id,
                'year_id' => $group->year_id,
                'organization_id' => $group->organization_id,
                'start_edu_contract' => $this->dateFrom,
                'stop_edu_contract' => $this->dateTo,
            ]);

            $contract->prodolj_d = '';
            $contract->prodolj_m = '';
            $contract->prodolj_m_user = '';

            $contract->first_m_price = '';
            $contract->other_m_price = '';

            $contract->first_m_nprice = '';
            $contract->other_m_nprice = '';*/

            return [
                'realizationPeriodInMonthes' => $realizationPeriodInMonthes,
                'realizationPeriodInDays' => $realizationPeriodInDays,
                'groupRealizationPeriod' => $groupRealizationPeriod,
                'normativePrice' => $normativePrice,
                'daysInFirstMonth' => $daysInFirstMonth,
                'firstMonthNormativePrice' => $firstMonthNormativePrice,
                'otherMonthesNormativePrice' => $otherMonthesNormativePrice,
                'moduleNormativePrice' => $group->module->normative_price,
            ];
        }
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
}
