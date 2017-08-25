<?php

namespace app\models\forms;

use app\helpers\CalculationHelper;
use app\models\OperatorSettings;
use app\models\Payers;
use app\models\ProgrammeModule;
use Yii;
use yii\base\Model;

/**
 * Class ModuleUpdateForm
 * @package app\models\forms
 */
class ModuleUpdateForm extends Model
{
    public $dateFrom;
    public $dateTo;
    public $price;
    public $firstConfirm;
    public $secondConfirm;

    private $model;
    private $settings;
    private $payer;

    /**
     * ModuleUpdateForm constructor.
     * @param integer $moduleId
     * @param array $config
     */
    public function __construct($moduleId, $config = [])
    {
        $this->setModel($moduleId);
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['price'], 'required'],
            [['dateFrom', 'dateTo'], 'safe'],
            [
                'firstConfirm', 'required', 'requiredValue' => 1,
                'when' => function ($model) {
                    return $model->price > $model->getModel()->normative_price;
                },
                'message' => 'Необходимо подтвердить действие'
            ],
            [
                'secondConfirm', 'required', 'requiredValue' => 1,
                'when' => function ($model) {
                    return $model->price > $model->calculateRecommendedPrice();
                },
                'message' => 'Необходимо подтвердить действие'
            ],
            ['price', 'number'],
            ['price', 'validateData']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'price' => 'Установите цену модуля:',
            'firstConfirm' => 'Я подтверждаю, что осознанно устанавливаю стоимость модуля, превышающую нормативную 
                стоимость, что потребует оплаты части стоимости модуля со стороны родителей',
            'secondConfirm' => 'Я подтверждаю, что осознанно устанавливаю стоимость, 
                которая возможно не будет покрыта полностью за счет средств сертификата детей в муниципальном районе 
                (городском округе), в котором реализуется программа',
            'dateFrom' => 'Дата начала',
            'dateTo' => 'Дата конца'
        ];
    }

    /**
     * @param $attribute
     */
    public function validateData($attribute)
    {
        if ($this->getModel()->getContracts()->andWhere(['contracts.status' => [0,1,3]])->count() > 0) {
            $this->addError($attribute, 'Нельзя изменить цену программы, есть заявка на эту программу');
        }
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (null !== ($model = $this->getModel())) {
            $model->price = $this->price;

            return $model->save(false);
        }

        return false;
    }

    /**
     * (расчет: если дата конца текущего периода < дата начала группы, то 0%, иначе -
     * (дата конца текущего текущего периода - дата начала группы +1)/(дата конца группы - дата начала группы +1))
     */
    public function calculateCurrentPercent()
    {
        if (strtotime($this->getSettings()->current_program_date_to) < strtotime($this->dateFrom)) {
            $currentPercent = 0;
        } else {
            $dateFrom = max(strtotime($this->dateFrom), strtotime($this->getSettings()->current_program_date_from));
            $dateTo = min(strtotime($this->dateTo), strtotime($this->getSettings()->current_program_date_to));
            $currentPercent = CalculationHelper::daysBetweenDates(
                date('Y-m-d', $dateFrom),
                date('Y-m-d', $dateTo)
            ) / CalculationHelper::daysBetweenDates($this->dateTo, $this->dateFrom) * 100;
        }

        return $currentPercent;
    }

    /**
     * (расчет: если дата конца группы < дата начала будущего периода, то 0%, иначе:
     * (дата конца группы - дата начала будущего периода+1)/(дата конца группы - дата начала группы +1) )
     */
    public function calculateFuturePercent()
    {
        if (strtotime($this->getSettings()->future_program_date_from) > strtotime($this->dateTo)) {
            $futurePercent = 0;
        } else {
            $dateFrom = max(strtotime($this->dateFrom), strtotime($this->getSettings()->future_program_date_from));
            $dateTo = min(strtotime($this->dateTo), strtotime($this->getSettings()->future_program_date_to));
            $futurePercent = CalculationHelper::daysBetweenDates(
                date('Y-m-d', $dateFrom),
                date('Y-m-d', $dateTo)
            ) / CalculationHelper::daysBetweenDates($this->dateTo, $this->dateFrom) * 100;
        }

        return $futurePercent;
    }

    /**
     * (минимум из максимальных цен для текущего и будущего периода: мин(номинал текущий/долю программы в
     * текущем периоде; номинал будущего периода/доля программы в будущем периоде). Округляем вниз до рубля)
     */
    public function calculateRecommendedPrice()
    {
        $certGroup = $this->getPayer()->firstCertGroup;
        if ($this->calculateCurrentPercent() === 0 && $this->calculateFuturePercent() !== 0) {
            $recommendedPrice = $certGroup->nominal_f / $this->calculateFuturePercent() * 100;
        } elseif ($this->calculateFuturePercent() === 0 && $this->calculateCurrentPercent() !== 0) {
            $recommendedPrice = $certGroup->nominal / $this->calculateCurrentPercent() * 100;
        } else {
            $recommendedPrice = min(
                $certGroup->nominal / $this->calculateCurrentPercent() * 100,
                $certGroup->nominal_f / $this->calculateFuturePercent() * 100
            );
        }

        return $recommendedPrice;
    }

    /**
     * @param integer $moduleId
     * @throws \DomainException
     */
    public function setModel($moduleId)
    {
        $this->model = ProgrammeModule::find()
            ->joinWith(['program'])
            ->andWhere([
                'years.id' => $moduleId,
                'programs.organization_id' => Yii::$app->user->getIdentity()->organization->id,
            ])
            ->one();
        if (null === $this->model) {
            throw new \DomainException('Model not found');
        }
        $this->price = $this->model->price ?: null;
        if ($group = $this->model->groups[0]) {
            $this->dateFrom = date('d.m.Y', strtotime($group->datestart));
            $this->dateTo = date('d.m.Y', strtotime($group->datestop));
        }
    }

    /**
     * @return ProgrammeModule|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return OperatorSettings|null
     */
    public function getSettings()
    {
        if (null === $this->settings) {
            $this->settings = Yii::$app->operator->identity->settings;
        }

        return $this->settings;
    }

    /**
     * @return Payers|null
     */
    public function getPayer()
    {
        if (null === $this->payer) {
            $this->payer = $this->getModel()->program->municipality->payer;
        }

        return $this->payer;
    }
}
