<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 08.10.2017
 * Time: 12:03
 */

namespace app\models\certificates;


use app\components\SingleModelActions;
use app\models\Certificates;
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
abstract class CertificateActions extends SingleModelActions
{

    public static function getTargetModelClass(): string
    {
        return Certificates::className();
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
     * @return yii\db\ActiveRecord|null
     */
    public function getCertificate()
    {
        return $this->targetModel;
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
        return parent::setTargetModel($certificate);
    }

}