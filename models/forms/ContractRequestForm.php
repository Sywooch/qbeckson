<?php

namespace app\models\forms;

use app\models\Certificates;
use app\models\Contracts;
use app\models\contracts\ContractRequest;
use app\models\Groups;
use app\models\Payers;
use Yii;
use yii\base\Model;

/**
 * Class ContractRequestForm
 * @package app\models\forms
 */
class ContractRequestForm extends Model
{
    public $dateFrom;
    public $dateTo;

    private $group;
    private $contract;
    private $certificate;

    /**
     * @var ContractRequest
     */
    private $contractRequest;

    /**
     * ContractRequestForm constructor.
     * @param integer $groupId
     * @param null $certificateId
     * @param Contracts|null $contract
     * @param array $config
     */
    public function __construct($groupId, $certificateId = null, $contract = null, $config = [])
    {
        $this->contractRequest = new ContractRequest();

        $this->setGroup($groupId);
        $this->setContract($contract);
        $this->setCertificate($certificateId);
        if (null === $this->dateFrom) {
            if (time() < strtotime($this->getGroup()->datestart)) {
                $this->dateFrom = Yii::$app->formatter->asDate($this->getGroup()->datestart);
            } else {
                $this->dateFrom = date('d.m.Y', strtotime('next day'));
            }
        }
        parent::__construct($config);
    }

    /**
     * TODO Сделать вализацию группы при подаче договора, а не только на фронэнде.
     *
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
    public function rules(): array
    {
        return [
            [['dateFrom'], 'required'],
            [['dateFrom'], 'date', 'format' => 'php:d.m.Y'],
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
        /*if (strtotime($this->$attribute) < time()) {
            $this->addError($attribute, 'Дата начала должна быть больше или равна текущей дате');
            return;
        }*/

        /** @var Payers $payer */
        $payer = $this->getCertificate()->payer;

        $group = $this->getGroup();
        $this->contractRequest->setStartEduContract($this->dateFrom);
        if (!$this->contractRequest->validate(
            $group->datestart,
            $group->datestop,
            $payer->certificateCanUseCurrentBalance(),
            $payer->certificate_can_use_future_balance
        )
        ) {
            $this->addError($attribute, $this->contractRequest->errorMessage);
        }
    }

    /**
     * @return Contracts|boolean
     */
    public function save()
    {
        if (is_null($group = $this->getGroup()) || is_null($certificate = $this->getCertificate())) {
            return false;
        }

        $contractRequestData = $this->contractRequest->getData(
            $group->datestart,
            $group->datestop,
            $group->module->price,
            $group->module->normative_price,
            $group->id,
            $group->program_id,
            $group->year_id,
            $group->organization_id,
            $certificate->id,
            $certificate->payer_id,
            $certificate->number,
            $certificate->balance,
            $certificate->balance_f
        );

        if (is_null($contractRequestData) || is_null($contract = $this->getContract())) {
            return false;
        }

        $contract->setAttributes($contractRequestData);

        return $contract->save() ? $contract : false;
    }

    /**
     * @return Contracts
     */
    public function getContract(): Contracts
    {
        return $this->contract;
    }

    /**
     * @return Groups
     */
    public function getGroup(): Groups
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
            throw new \DomainException('Group not found!');
        }
    }

    /**
     * @param integer|null $certificateId
     */
    private function setCertificate($certificateId)
    {
        $this->certificate = (null === $certificateId) ?
            Yii::$app->user->getIdentity()->certificate : Certificates::findOne($certificateId);
        if (null === $this->certificate) {
            throw new \DomainException('Certificate not found!');
        }
    }

    /**
     * @return Certificates
     */
    private function getCertificate(): Certificates
    {
        return $this->certificate;
    }

    /**
     * @param Contracts|null $contract
     */
    public function setContract($contract)
    {
        if (null !== $contract) {
            $this->contract = $contract;
            $this->dateFrom = date('d.m.Y', strtotime($contract->start_edu_contract));
            $this->dateTo = date('d.m.Y', strtotime($contract->stop_edu_contract));
        } else {
            $this->contract = new Contracts();
        }
    }
}
