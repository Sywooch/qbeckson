<?php

namespace app\models\contracts;


use app\models\Certificates;
use app\models\Completeness;
use app\models\Contracts;
use app\models\Groups;
use app\models\Organization;
use app\models\Programs;
use yii\validators\InlineValidator;

/**
 * Class ContractVerificator
 * @package app\models\contracts
 * @property int $applicationIsReceived
 * @property int $status
 *
 * @property Certificates $certificate
 * @property Groups $group
 * @property Programs $program
 */
class ContractOrganizationVerificator extends ContractsActions
{
    public $date;
    private $currentMonth;
    private $nextMonth;
    private $lastDayOfMonth;

    public static function build($config): self
    {
        return new static($config);
    }

    public function init()
    {
        parent::init();
        $this->date = $this->contract->date;
        $this->currentMonth = strtotime('first day of this month');
        $this->nextMonth = strtotime('first day of next month');
        $this->lastDayOfMonth = strtotime('last day of this month');
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['number', 'date', 'applicationIsReceived'], 'required'],
            ['applicationIsReceived', 'required', 'requiredValue' => 1,],
            ['date', 'date', 'format' => 'php:Y-m-d'],
            ['number', 'string'],
            ['number', 'contractNumberValidator'],
        ]);
    }

    public function contractNumberValidator($attribute, $params, InlineValidator $validator)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $this->date);
        $beginYear = $date->modify('first day of January')->format('Y-m-d');
        $endYear = $date->modify('last day of December')->format('Y-m-d');
        if ($this
            ->contract
            ->organization
            ->getContracts()
            ->andWhere(['status' => Contracts::STATUS_ACTIVE])
            ->andWhere(['number' => $this->number])
            ->andWhere(['BETWEEN', 'date', $beginYear, $endYear])
            ->exists()) {
            $this->addError($attribute, 'Договор с таким номером уже существует!');
        }
    }

    public function getStatus(): int
    {
        return $this->contract->status;
    }

    public function getApplicationIsReceived(): int
    {
        return $this->contract->applicationIsReceived;
    }

    public function setApplicationIsReceived(int $applicationIsReceived)
    {
        $this->contract->applicationIsReceived = $applicationIsReceived;
    }

    public function getCertificate(): Certificates
    {
        return $this->contract->certificate;
    }

    public function getGroup(): Groups
    {
        return $this->contract->group;
    }

    public function getProgram(): Programs
    {
        return $this->contract->program;
    }

    public function getId(): int
    {
        return $this->contract->id;
    }

    public function getNumber(): string
    {
        return $this->contract->number;
    }

    /** readonly ни чего не делаем*/
    public function setNumber()
    {
    }

    /**
     * Все манипуляции внутри этой функции происходят в трансзакции, можно прервать трансзакцию из нутри.
     * для успешного завершения вернуть true
     *
     * Реализовано на основе механизма ленивых вычислений.
     *
     * @param \Closure $transactionTerminator
     * @param bool $validate
     *
     * @return bool
     */
    public function saveActions(\Closure $transactionTerminator, bool $validate): bool
    {
        return (
                (
                    $this->organizationChildAmountInc($this->contract->organization)
                    || $this->addError('contract', 'Не удалось установить amount_child у организации')
                )
                && (
                    $this->organizationChildAmountInc($this->contract->organization)
                    || $this->addError('contract', 'Не удалось установить amount_child у организации')
                )
                && (
                    $this->programLastContractsInc($this->contract->program)
                    || $this->addError('contract', 'Не удалось установить last_contracts у программы')
                )
                && (
                    $this->certificateRezervDec($this->contract->certificate)
                    || $this->addError('contract', 'Не удалось установить пересчитать резерв сертификата')
                )
                && (
                    $this->contractCalcRezervAndPaid()
                    || $this->addError('contract', 'Не удалось пересчитать резервы договора')
                )
                && (
                    $this->contractCalcStatusAndTerminateState()
                    || $this->addError('contract', 'Не удалось установить статус договора')
                )
                && (
                    ((date('m') == 1 || $this->contract->start_edu_contract >= date('Y-m-d', $this->currentMonth))
                        || $this->buildAndSaveCompleteness())
                    || $this->addError('contract', 'Не удалось создать и сохранить completeness')
                )
                && (
                (($this->contract->start_edu_contract >= date('Y-m-d', $this->nextMonth))
                    || (($this->buildAndSavePreinvoice()
                            || $this->addError('contract', 'Не удалось создать и сохранить Preinvoice'))
                        && (
                        true/**  todo доделать */
                        ))
                )

                )


            )
            || $transactionTerminator();
    }

    private function organizationChildAmountInc(Organization $organization): bool
    {
        $organization->amount_child++;

        return $organization->save();
    }

    private function programLastContractsInc(Programs $programs): bool
    {
        $programs->last_contracts++;

        return $programs->save();
    }

    /**
     * todo добавить описание происходящего и назначения полей
     *
     * @param Certificates $certificates
     *
     * @return bool
     */
    private function certificateRezervDec(Certificates $certificates): bool
    {
        $result = true;
        if ($this->contract->period == Contracts::CURRENT_REALIZATION_PERIOD) {
            $result = $result && $certificates->updateCounters([
                    'rezerv' => $this->contract->payer_first_month_payment * -1,
                ]);

            if ($this->isExtensionContract($this->contract)) {
                $result = $result && $certificates->updateCounters([
                        'rezerv' => $this->contract->payer_other_month_payment * -1,
                    ]);
            }
        } elseif ($this->contract->period == Contracts::FUTURE_REALIZATION_PERIOD) {
            $result = $result && $certificates->updateCounters([
                    'rezerv_f' => $this->contract->payer_first_month_payment * -1,
                ]);
        }

        return $result;
    }


    /**
     * Договор находится в режиме продления?
     *
     * @param Contracts $contracts
     *
     * @return bool
     */
    private function isExtensionContract(Contracts $contracts): bool
    {
        return $contracts->start_edu_contract < date('Y-m-d', $this->currentMonth)
            && $contracts->prodolj_m_user > 1;
    }

    private function contractCalcRezervAndPaid(): bool
    {
        $this->contract->paid = $this->contract->payer_first_month_payment;
        $this->contract->rezerv = $this->contract->rezerv - ($this->contract->payer_first_month_payment);
        if ($this->isExtensionContract($this->contract)) {
            $this->contract->paid += $this->contract->payer_other_month_payment;
            $this->contract->rezerv -= $this->contract->payer_other_month_payment;
        }

        return true;
    }

    private function contractCalcStatusAndTerminateState(): bool
    {
        $this->contract->status = Contracts::STATUS_ACTIVE;
        if ($this->contract->stop_edu_contract <= date('Y-m-d', $this->lastDayOfMonth)) {
            $this->contract->wait_termnate = 1;
        }

        return true;
    }

    private function buildAndSaveCompleteness(): bool
    {
        if (date('m') == 1 || $this->contract->start_edu_contract >= date('Y-m-d', $this->currentMonth)) {
            return true;
        }

        $completeness = new Completeness();
        $completeness->group_id = $this->contract->group_id;
        $completeness->contract_id = $this->contract->id;

        $start_edu_contract = explode("-", $this->contract->start_edu_contract);

        if (date('m') == 12) {
            $completeness->month = date('m');
            $completeness->year = $start_edu_contract[0];
        } else {
            $completeness->month = date('m') - 1;
            $completeness->year = $start_edu_contract[0];
        }
        $completeness->preinvoice = 0;
        $completeness->completeness = 100;

        $month = $start_edu_contract[1];

        if (date('m') == 12) {
            if ($month == 12) {
                $price = $this->contract->payer_first_month_payment;
            } else {
                $price = $this->contract->payer_other_month_payment;
            }
        } else {
            if ($month == date('m') - 1) {
                $price = $this->contract->payer_first_month_payment;
            } else {
                $price = $this->contract->payer_other_month_payment;
            }
        }
        $completeness->sum = round(($price * $completeness->completeness) / 100, 2);

        return $completeness->save();
    }

    private function buildAndSavePreinvoice(): bool
    {
        $preinvoice = new Completeness();
        $preinvoice->group_id = $this->contract->group_id;
        $preinvoice->contract_id = $this->contract->id;
        $preinvoice->month = date('m');
        $preinvoice->year = $this->start_edu_contract[0];
        $preinvoice->preinvoice = 1;
        $preinvoice->completeness = 80;
        $month = $this->start_edu_contract[1];

        if ($month == date('m')) {
            $price = $this->contract->payer_first_month_payment;
        } else {
            $price = $this->contract->payer_other_month_payment;
        }
        $preinvoice->sum = round(($price * $preinvoice->completeness) / 100, 2);

        return $preinvoice->save();
    }
}
