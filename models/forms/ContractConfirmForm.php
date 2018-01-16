<?php

namespace app\models\forms;

use app\models\Certificates;
use app\models\Contracts;
use app\models\Organization;
use Yii;
use yii\base\Model;

/**
 * Class ContractConfirmForm
 * @package app\models\forms
 */
class ContractConfirmForm extends Model
{
    public $firstConfirmation;
    public $secondConfirmation;
    public $thirdConfirmation;

    private $contract;
    private $certificate;

    /**
     * ContractConfirmForm constructor.
     * @param Contracts $contract
     * @param null $certificateId
     * @param array $config
     */
    public function __construct(Contracts $contract, $certificateId = null, $config = [])
    {
        $this->setContract($contract);
        $this->setCertificate($certificateId);
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                ['firstConfirmation', 'thirdConfirmation'],
                'required', 'requiredValue' => 1,
                'message' => 'Вы должны подтвердить своё согласие'
            ],
            [
                'secondConfirmation', 'required', 'requiredValue' => 1, 'when' => function () {
                    return $this->getContract()->all_parents_funds > 0;
                },
                'message' => 'Вы должны подтвердить своё согласие'
            ],
            ['thirdConfirmation', 'validateConfirmation'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'firstConfirmation' => 'Действительно хочу подать заявку',
            'secondConfirmation' => 'Я осознаю и соглашаюсь, что в соответствии с условиями договора предусмотрено 
                частичное финансирование услуги по дополнительному образованию за счет моих средств',
            'thirdConfirmation' => 'Я ознакомился с условиями обучения, в том числе со сроками обучения, условиями 
                оплаты за счет средств сертификата и хочу подать заявку',
        ];
    }

    /**
     * @param $attribute
     */
    public function validateConfirmation($attribute)
    {
        $balance = $this->getCertificate()->balance_f;
        if ($this->getContract()->period == Contracts::CURRENT_REALIZATION_PERIOD) {
            $balance = $this->getCertificate()->balance;
        }

        if ((int)$this->getContract()->balance != (int)$balance) {
            $this->addError(
                $attribute,
                'К сожалению заявка не может быть направлена в организацию, поскольку после последнего расчета значение Вашего номинала было изменено. Пересчитайте параметры заявки.'
            );
        }
    }

    /**
     * @return boolean
     */
    public function save(): bool
    {
        if (null !== ($contract = $this->getContract()) && null !== ($certificate = $this->getCertificate())) {
            //TODO Обернуть в транзакцию!!!
            $organization = $contract->organization;
            $organization->updateCounters(['contracts_count' => 1]);

            $contract->number = $organization->getContractNumber();
            $contract->date = date('Y-m-d');
            $contract->status = Contracts::STATUS_REQUESTED;
            $contract->requested_at = date('Y-m-d H:i:s');
            $contract->rezerv = $contract->funds_cert;
            $contract->setCooperate();
            $contract->save(false, [
                'number',
                'date',
                'status',
                'requested_at',
                'rezerv',
                'cooperate_id',
            ]);

            if ($contract->period == Contracts::CURRENT_REALIZATION_PERIOD) {
                $certificate->updateCounters([
                    'balance' => $contract->funds_cert * -1,
                    'rezerv' => $contract->funds_cert,
                ]);
            } elseif ($contract->period === Contracts::FUTURE_REALIZATION_PERIOD) {
                $certificate->updateCounters([
                    'balance_f' => $contract->funds_cert * -1,
                    'rezerv_f' => $contract->funds_cert,
                ]);
            } else {
                throw new \DomainException('Period not found');
            }

            return true;
        }

        return false;
    }

    /**
     * @return Contracts
     */
    public function getContract(): Contracts
    {
        return $this->contract;
    }

    /**
     * @param integer|null $certificateId
     */
    public function setCertificate($certificateId)
    {
        $this->certificate = (null === $certificateId) ?
            Yii::$app->user->getIdentity()->certificate : Certificates::findOne($certificateId);
    }

    /**
     * @return Certificates
     */
    public function getCertificate(): Certificates
    {
        return $this->certificate;
    }

    /**
     * @param Contracts $contract
     */
    public function setContract(Contracts $contract)
    {
        $this->contract = $contract;
    }
}
