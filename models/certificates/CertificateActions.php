<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 08.10.2017
 * Time: 12:03
 */

namespace app\models\certificates;


use app\models\Certificates;
use app\models\Model;
use yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * Class CertificateActions
 * @package app\models\certificates
 *
 * @property string $firstErrorAsString
 * @property Certificates $certificate
 */
abstract class CertificateActions extends Model
{
    /**
     * @var Certificates || null
     */
    private $_certificate;

    /**
     * CertificateActions constructor.
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
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'certificate' => 'Сертификат',

        ]);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['certificate'], 'required'],
        ];
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
     * @throws yii\db\Exception
     */
    public function save($validate = true)
    {
        if ($validate && (!$this->validate() || !$this->certificate->validate())) {
            return false;
        }
        $trans = Yii::$app->db->beginTransaction();
        $rollback = function () use ($trans)
        {
            $trans->rollBack();

            return false;
        };

        if ($this->saveActions($rollback, $validate) && $this->certificate->save($validate)) {
            $trans->commit();

            return true;
        }

        return $rollback();


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
    public abstract function saveActions(\Closure $transactionTerminator, bool $validate): bool;
}