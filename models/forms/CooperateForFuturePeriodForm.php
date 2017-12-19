<?php

namespace app\models\forms;

use app\models\Cooperate;
use app\models\Model;
use app\models\OperatorSettings;
use Yii;

/**
 * форма создания соглашения для будущего периода
 */
class CooperateForFuturePeriodForm extends Model
{
    /**
     * тип соглашения
     *
     * @var string
     */
    public $type;

    /**
     * использовать тип такой же как у соглашения текущего периода действия
     *
     * @var boolean
     */
    public $useCurrentCooperateType;

    /**
     * максимальная сумма соглашения
     *
     * @var integer
     */
    public $maximumAmount;

    /**
     * соглашение текущего периода (cooperate.period = 1)
     *
     * @var Cooperate
     */
    public $currentPeriodCooperate;

    /**
     * соглашение будущего периода
     *
     * @var Cooperate
     */
    public $futurePeriodCooperate;

    /**
     * @var OperatorSettings
     */
    public $operatorSettings;

    /**
     * @var string
     */
    public $futureCooperateNumber;

    /**
     * @var string
     */
    public $futureCooperateDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['useCurrentCooperateType', 'required'],
            [['futureCooperateNumber', 'futureCooperateDate'], 'required', 'when' => function () {
                if (1 == $this->useCurrentCooperateType) {
                    return false;
                }

                return true;
            }],
            ['maximumAmount', 'required', 'when' => function () {
                if (1 == $this->useCurrentCooperateType) {
                    if (Cooperate::DOCUMENT_TYPE_EXTEND == $this->currentPeriodCooperate->document_type) {
                        return true;
                    }
                } else {
                    if (Cooperate::DOCUMENT_TYPE_EXTEND == $this->type) {
                        return true;
                    }
                }

                return false;
            }],
            ['maximumAmount', 'integer'],
            ['type', 'in', 'range' => array_keys(Cooperate::documentTypes())],
            [['futureCooperateNumber', 'futureCooperateDate'], 'string'],
            ['cooperateFile', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'useCurrentCooperateType' => 'Использовать соглашение текущего периода (пролонгация действия текущего соглашения)',
            'type' => 'Выберите тип документа (генерируется в формате Word 2007):',
            'cooperateWithMaximumAmount' => 'В договоре устанавливается максимальная сумма',
            'maximumAmount' => 'Укажите сумму средств по договору ' . Cooperate::documentNames()[Yii::$app->operator->identity->settings->document_name] . ', для оплаты услуг в будущем периоде (с ' . \Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_from) . ' по ' . \Yii::$app->formatter->asDate(Yii::$app->operator->identity->settings->future_program_date_to) . ')',
            'cooperateFile' => 'Документ',
            'futureCooperateNumber' => 'Номер соглашения',
            'futureCooperateDate' => 'Дата заключения соглашения',
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $currentCooperate = $this->getCurrentPeriodCooperate();
        $futurePeriodCooperate = $this->getFuturePeriodCooperate();
        if (!$futurePeriodCooperate || !$currentCooperate) {
            return $futurePeriodCooperate;
        }

        $futurePeriodCooperate->load(['Cooperate' => $currentCooperate->getAttributes()]);

        if ($this->useCurrentCooperateType && $currentCooperate) {
            $futurePeriodCooperate->total_payment_limit = $this->maximumAmount;
            $futurePeriodCooperate->period = Cooperate::PERIOD_FUTURE;
            if ($futurePeriodCooperate->save()) {
                return true;
            }

            $this->addErrors($futurePeriodCooperate->getErrors());

            return false;
        }

        $futurePeriodCooperate->document_type = $this->type;

        if ($this->type === Cooperate::DOCUMENT_TYPE_GENERAL) {
            $futurePeriodCooperate->total_payment_limit = null;

            $futurePeriodCooperate->document = [
                'path' => $this->getOperatorSettings()->general_document_path,
                'base_url' => $this->getOperatorSettings()->general_document_base_url,
            ];
        }
        if ($this->type === Cooperate::DOCUMENT_TYPE_EXTEND) {
            $futurePeriodCooperate->total_payment_limit = $this->maximumAmount;

            $futurePeriodCooperate->document = [
                'path' => $this->getOperatorSettings()->extend_document_path,
                'base_url' => $this->getOperatorSettings()->extend_document_base_url,
            ];
        }

        $futurePeriodCooperate->status = Cooperate::STATUS_ACTIVE;
        $futurePeriodCooperate->created_date = date('Y-m-d H:i:s');
        $futurePeriodCooperate->date = $this->futureCooperateDate;
        $futurePeriodCooperate->number = $this->futureCooperateNumber;
        $futurePeriodCooperate->period = Cooperate::PERIOD_FUTURE;

        if ($futurePeriodCooperate->save()) {
            return true;
        }

        $this->addErrors($futurePeriodCooperate->getErrors());

        return false;
    }

    /**
     * @return Cooperate
     */
    public function getCurrentPeriodCooperate()
    {
        return $this->currentPeriodCooperate;
    }

    /**
     * @param Cooperate $cooperate
     */
    public function setCurrentPeriodCooperate($cooperate)
    {
        $this->currentPeriodCooperate = $cooperate;
    }

    /**
     * получить соглашение для будущего периода
     */
    public function getFuturePeriodCooperate()
    {
        return $this->futurePeriodCooperate;
    }

    /**
     * @return OperatorSettings
     */
    public function getOperatorSettings()
    {
        if (null === $this->operatorSettings) {
            $this->operatorSettings = Yii::$app->operator->identity->settings;
        }

        return $this->operatorSettings;
    }

    /**
     * указать соглашение для будущего периода
     *
     * @internal param $organizationId
     *
     * @param $organizationId
     *
     * @return bool
     */
    public function createFuturePeriodCooperate($organizationId)
    {
        if (Cooperate::findOne([
            'organization_id' => $organizationId,
            'payer_id' => \Yii::$app->user->identity->payer->id,
            'period' => Cooperate::PERIOD_FUTURE,
            'status' => Cooperate::STATUS_ACTIVE
        ])) {
            return false;
        }

        $this->futurePeriodCooperate = new Cooperate(['payer_id' => \Yii::$app->user->identity->payer->id, 'organization_id' => $organizationId]);

        return true;
    }
}
