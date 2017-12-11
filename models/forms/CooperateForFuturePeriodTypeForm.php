<?php

namespace app\models\forms;

use app\models\Cooperate;
use app\models\Model;
use app\models\OperatorSettings;
use Yii;

/**
 * форма изменения типа соглашения, действующего в будущем периоде
 */
class CooperateForFuturePeriodTypeForm extends Model
{
    /**
     * тип соглашения
     *
     * @var string
     */
    public $type;

    /**
     * максимальная сумма соглашения
     *
     * @var integer
     */
    public $maximumAmount;

    /**
     * @var Cooperate
     */
    public $cooperate;

    /**
     * @var OperatorSettings
     */
    public $operatorSettings;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['type', 'in', 'range' => [Cooperate::DOCUMENT_TYPE_GENERAL, Cooperate::DOCUMENT_TYPE_EXTEND]],
            ['maximumAmount', 'integer'],
            ['maximumAmount', 'required', 'when' => function () {
                /** @var self $model */
                return Cooperate::DOCUMENT_TYPE_EXTEND == $this->type;
            }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Выберите тип документа (генерируется в формате Word 2007):',
            'maximumAmount' => 'Укажите сумму средств по договору',
        ];
    }

    /**
     * изменить тип соглашения
     */
    public function changeCooperateType()
    {
        if (null !== ($model = $this->getCooperate()) && $this->validate()) {
            if ($model->document_type != Cooperate::DOCUMENT_TYPE_CUSTOM) {
                $model->setOldAttribute('document_path', null);
                $model->setOldAttribute('document_base_url', null);
            }

            $model->document_type = $this->type;
                if ($this->type === Cooperate::DOCUMENT_TYPE_GENERAL) {
                    $model->total_payment_limit = null;

                    $model->document = [
                        'path' => $this->getOperatorSettings()->general_document_path,
                        'base_url' => $this->getOperatorSettings()->general_document_base_url,
                    ];
                }
                if ($this->type === Cooperate::DOCUMENT_TYPE_EXTEND) {
                    $model->total_payment_limit = $this->maximumAmount;

                    $model->document = [
                        'path' => $this->getOperatorSettings()->extend_document_path,
                        'base_url' => $this->getOperatorSettings()->extend_document_base_url,
                    ];
                }

            return $model->save();
        }

        return false;
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
     * @return Cooperate
     */
    public function getCooperate()
    {
        return $this->cooperate;
    }

    /**
     * @param Cooperate $cooperate
     */
    public function setCooperate($cooperate)
    {
        $this->cooperate = $cooperate;
    }
}