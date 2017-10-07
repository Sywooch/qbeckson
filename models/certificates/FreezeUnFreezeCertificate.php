<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 07.10.2017
 * Time: 9:59
 */

namespace app\models\certificates;


use app\components\validators\RequiredXOR;
use app\models\Certificates;
use app\models\Contracts;
use app\models\Model;
use app\models\UserIdentity;
use yii;
use yii\base\InvalidParamException;

/**
 * Class FreezeUnFreezeCertificate
 * @package app\models\certificates
 *
 * @property bool $canFreeze
 * @property bool $canUnFreeze
 * @property string $firstErrorAsString
 * @property Certificates $certificate
 */
class FreezeUnFreezeCertificate extends Model
{
    /**
     * @var bool
     */
    public $freeze;
    /**
     * @var bool
     */
    public $unFreeze;

    /**
     * @var Certificates || null
     */
    private $_certificate;


    /**
     * FreezeUnFreezeCertificate constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!is_array($config)) {
            parent::__construct(['certificate' => $config]);

            return;
        }
        parent::__construct($config);
    }

    /**
     * @param $certificate Certificates || integer
     *
     * @return self
     */
    public static function getFreezer($certificate)
    {
        $instance = new self($certificate);
        $instance->freeze = true;

        return $instance;
    }

    /**
     * @param $certificate Certificates || integer
     *
     * @return self
     */
    public static function getUnFreezer($certificate)
    {
        $instance = new self($certificate);
        $instance->unFreeze = true;

        return $instance;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['certificate'], 'required'],
            [['freeze', 'unFreeze'], 'boolean'],
            [['unFreeze'], 'unFreezeValidator'],
            [['freeze'], 'freezeValidator'],
            [['freeze', 'unFreeze'], RequiredXOR::className(), 'requiredValue' => true, 'strict' => true],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @param yii\validators\InlineValidator $validator
     */
    public function freezeValidator($attribute, $params, yii\validators\InlineValidator $validator)
    {
        if ($this->freeze) {
            $this->getCanFreeze();
        }
    }

    /**
     * @param $emitErrors bool Записать шибки валидации в модель
     *
     * @return bool
     */
    public function getCanFreeze($emitErrors = true): bool
    {
        if (!$this->certificate->actual) {
            $emitErrors && $this->addError('freeze',
                'Невозможно заморозить, сертификат заморожен в настоящий момент');

            return false;
        }
        $contractsExists = $this->certificate->getContractsModels()
            ->where(['OR', ['status' => Contracts::STATUS_ACTIVE], ['wait_termnate' => 1]])
            ->exists();

        if ($contractsExists) {
            $emitErrors && $this->addError('freeze',
                'Невозможно заморозить, существуют активные контракты');

            return false;
        }

        return true;
    }

    /**
     * @param $attribute
     * @param $params
     * @param yii\validators\InlineValidator $validator
     */
    public function unFreezeValidator($attribute, $params, yii\validators\InlineValidator $validator)
    {
        if ($this->unFreeze) {
            $this->getCanUnFreeze();
        }
    }

    /**
     * @param $emitErrors bool Записать шибки валидации в модель
     *
     * @return bool
     */
    public function getCanUnFreeze($emitErrors = true): bool
    {
        if ($this->certificate->actual) {
            $emitErrors && $this->addError('unFreeze',
                'Невозможно активировать, сертификат актуален в настоящий момент');

            return false;
        }
        if ($this->certificate->friezed_at >= Yii::$app->operator->identity->settings->current_program_date_from) {
            $emitErrors && $this->addError('unFreeze',
                'Невозможно активировать, сертификат заморожен в этом периоде, будет доступно в следующем.');

            return false;
        }
        if ($this->certificate->certGroup->hasVacancy()) {
            $emitErrors && $this->addError('unFreeze',
                'Невозможно активировать, достигнут лимит активынх сертификатов.');

            return false;

        }

        return true;
    }

    /**
     * @return Certificates|null
     */
    public function getCertificate()
    {
        return $this->_certificate;
    }

    /**
     *
     * @param Certificates||integer $certificate
     *
     * @throws InvalidParamException
     *
     */
    public function setCertificate($certificate)
    {
        if ($certificate instanceof Certificates) {
            $this->_certificate = $certificate;
        } elseif (is_scalar($certificate)) {
            $this->_certificate = Certificates::findOne(['id' => $certificate]);
        } else {
            throw new InvalidParamException('Параметр должен быть экземпляром ' .
                Certificates::className() .
                ' либо целым числом (идентификатором сертификата)');
        }
    }

    /**
     * @param bool $validate
     *
     * @return bool
     * @throws yii\base\Exception
     * @throws yii\db\Exception
     */
    public function save($validate = true)
    {

        if ($validate && !$this->validate()) {
            return false;
        }
        $trans = Yii::$app->db->beginTransaction();
        $rollback = function () use ($trans)
        {
            $trans->rollBack();

            return false;
        };
        if ($this->freeze) {
            if (!$this->setFreeze()) {
                return $rollback();
            }
        } elseif ($this->unFreeze) {
            if (!$this->setUnFreeze()) {
                return $rollback();
            }
        } else {
            $rollback();

            throw new yii\base\Exception('не выбрано действие freeze/unfreeze');
        }
        if ($this->certificate->save($validate)) {
            $trans->commit();

            return true;
        }

        return $rollback();
    }

    private function setFreeze()
    {
        return $this->dropRequests()
            && $this->setFriezedData();
    }

    private function dropRequests()
    {
        $refuseCondition = [Contracts::tableName() . '.status' => [Contracts::STATUS_CREATED]];
        $contracts = $this->certificate->getContractsModels()->andWhere($refuseCondition)->all();

        return array_reduce($contracts, function ($acc, $contract)
            {
                /**@var $contract Contracts */
                return $acc && $contract->setRefused('Отклонено в связи с заморозкой сертификата.'
                        , UserIdentity::ROLE_PAYER_ID
                        , Yii::$app->user->identity->payer->id);
            }, true)
            || $this->addError('certificate', 'Не удалось отклонить все заявки сертификата.');

    }

    private function setFriezedData()
    {
        $this->certificate->friezed_ballance = $this->certificate->balance;
        $this->certificate->friezed_at = (new \DateTime())->format('Y-m-d');
        $this->certificate->actual = 0;
        $this->certificate->balance = 0;
        $this->certificate->nominal = 0;

        return true;
    }

    private function setUnFreeze()
    {
        $this->certificate->actual = 1;

        return true;
    }


    public function getFirstErrorAsString()
    {
        if ($this->hasErrors()) {
            return array_shift($this->getFirstErrors());
        }

        return null;
    }

}