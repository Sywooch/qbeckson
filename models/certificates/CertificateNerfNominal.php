<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 08.10.2017
 * Time: 12:01
 */

namespace app\models\certificates;


use app\models\Contracts;
use yii\helpers\ArrayHelper;
use yii\validators\InlineValidator;

/**
 * Class CertificateNerfNominal
 * @package app\models\certificates
 *
 * @property bool $canNerf
 */
class CertificateNerfNominal extends CertificateActions
{

    /**
     * для проверки только сертификата
     */
    const SCENARIO_ONLY_CERTIFICATE = 'scenarioOnlyCertificate';
    /**
     * @var double
     */
    public $newNominal;

    public function init()
    {
        parent::init();
        if (is_null($this->newNominal) && $this->certificate) {
            $this->newNominal = $this->certificate->nominal;
        }
    }

    /**
     * @return bool
     */
    public function getCanNerf()
    {
        $oldScenario = $this->getScenario();
        $this->setScenario(self::SCENARIO_ONLY_CERTIFICATE);
        $result = $this->validate();
        $this->setScenario($oldScenario);

        return $result;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'newNominal' => 'Номинал',
        ]);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                ['certificate', 'validateOnlyPF'],
                ['certificate', 'validateBalance'],
                ['certificate', 'validateContracts'],
                ['newNominal', 'required'],
                ['newNominal', 'number', 'max' => $this->certificate->nominal, 'min' => 0,
                    'message' => 'Значение должно быть числом. Используйте в качестве разделителя десятичных долей точку.'],
                //      ['newNominal', 'compare', 'operator' => '<' , 'compareValue' => $this->certificate->nominal],
            ]
        );

    }


    /**
     * @return array
     */
    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_ONLY_CERTIFICATE => ['certificate']
        ]);
    }

    /**
     * @param $attribute
     * @param $params
     * @param InlineValidator $validator
     */
    public function validateBalance($attribute, $params, InlineValidator $validator)
    {
        if ($this->certificate->balance !== $this->certificate->nominal) {
            $this->addError($attribute, 'Баланс сертификата отличается от текущего номинала.');
        }
    }

    /**
     * @param $attribute
     * @param $params
     * @param InlineValidator $validator
     */
    public function validateContracts($attribute, $params, InlineValidator $validator)
    {
        $contractsExists = $this->certificate->getContractsModels()
            ->where(['OR', ['status' => Contracts::STATUS_ACTIVE], ['wait_termnate' => 1]])
            ->exists();

        if ($contractsExists) {
            $this->addError($attribute, 'Невозможно изменить номинал, существуют активные контракты');
        }
    }

    /**
     * @param $attribute
     * @param $params
     * @param InlineValidator $validator
     */
    public function validateOnlyPF($attribute, $params, InlineValidator $validator)
    {
        if ($this->certificate->certGroup->is_special) {
            $this->addError($attribute, 'Невозможно изменить номинал, не верный тип сертификата, должен быть ПФ');
        }
    }

    /**
     * @param \Closure $transactionTerminator
     * @param bool $validate
     *
     * @return bool
     */
    public function saveActions(\Closure $transactionTerminator, bool $validate): bool
    {
        $this->certificate->nominal = $this->newNominal;
        $this->certificate->balance = $this->newNominal;

        return true;
    }

}