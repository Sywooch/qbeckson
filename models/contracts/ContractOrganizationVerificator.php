<?php

namespace app\models\contracts;


use app\models\Certificates;
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

    public static function build($config): self
    {
        return new static($config);
    }

    public function init()
    {
        parent::init();
        $this->date = $this->contract->date;
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

    private function organizationChildAmountInc(Organization $organization)
    {
        $organization->amount_child++;

        return $organization->save();
    }

    private function programLastContractsInc(Programs $programs)
    {
        $programs->last_contracts++;

        return $programs->save();
    }

    /**
     * Все манипуляции внутри этой функции происходят в трансзакции, можно прервать трансзакцию из нутри.
     * для успешного завершения вернуть true
     *
     * @param \Closure $transactionTerminator
     * @param bool $validate
     *
     * @return bool
     */
    public function saveActions(\Closure $transactionTerminator, bool $validate): bool
    {
        if (!$this->organizationChildAmountInc($this->contract->organization)) {
            $this->addError('contract', 'Не удалось установить amount_child у организации');

            return $transactionTerminator();
        }
        if (!$this->programLastContractsInc($this->contract->program)) {
            $this->addError('contract', 'Не удалось установить last_contracts у программы');

            return $transactionTerminator();
        }
        

    }

}
