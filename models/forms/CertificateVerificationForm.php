<?php

namespace app\models\forms;

use app\models\Certificates;
use app\models\Cooperate;
use Yii;
use yii\base\Model;

/**
 * Форма проверки подходит ли сертификат.
 *
 * Class CertificateVerificationForm
 * @package app\models\forms
 */
class CertificateVerificationForm extends Model
{
    public $number;
    public $soname;
    public $name;
    public $patronymic;

    private $certificate;
    private $cooperation;

    /**
     * Нет, ну я сейчас рефакторил код и там только:
     * 1) Существует ли вообще этот сртификат
     * 2) Существует ли соглашение между плательщиком сертификата и организацией
     * 3) Актулальный ли сертификат
     * 4) Число детей у организации(max_child) больше (amount_child) При этом я понятия не имею в какой момент amount_child увеличивается.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            [['number', 'soname', 'name', 'patronymic'], 'required'],
            [['number', 'soname', 'name', 'patronymic'], 'string'],
            ['number', 'validateData']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'number' => 'Номер сертификата',
            'soname' => 'Фамилия',
            'name' => 'Имя',
            'patronymic' => 'Отчество',
        ];
    }

    /**
     * TODO добавить проверку, является ли сертификат типом - ПФ.
     * Дополнительно бы добавить прверку. Реализована на фронтенде.
     * TODO Число детей у организации - max_child должно быть больше кол-ва договоров.
     *
     * @param string $attribute
     */
    public function validateData($attribute)
    {
        if (null === ($certificate = $this->getCertificate())) {
            $this->addError($attribute, 'Такого сертификата нет.');
            return;
        }
        if (null === ($cooperation = $this->getCooperation())) {
            $this->addError($attribute, 'Нет соглашения с плательщиком этого сертификата.');
            return;
        }
        if ($certificate->actual !== 1) {
            $this->addError($attribute, 'Сертификат заморожен.');
            return;
        }

        $payer = $certificate->payer;

        if (!$payer->certificateCanUseCurrentBalance() && $payer->certificate_can_use_future_balance != 1) {
            $this->addError($attribute, 'На данный момент уполномоченной организацией выбранного сертификата установлен запрет на создание новых заявок как в текущем, так и в будущем периоде.');
            return;
        }
    }

    /**
     * @return Cooperate|null
     */
    public function getCooperation()
    {
        if (null === $this->cooperation) {
            $this->cooperation = Cooperate::find()
                ->andWhere([
                    'organization_id' => Yii::$app->user->identity->organization->id,
                    'payer_id' => $this->getCertificate()->payer_id,
                    'status' => Cooperate::STATUS_ACTIVE,
                    'period' => [Cooperate::PERIOD_CURRENT, Cooperate::PERIOD_FUTURE],
                ])
                ->one();
        }

        return $this->cooperation;
    }

    /**
     * @return Certificates|null
     */
    public function getCertificate()
    {
        if (null === $this->certificate) {
            $this->certificate = Certificates::find()
                ->andWhere([
                    'number' => $this->number,
                    'name' => $this->name,
                    'soname' => $this->soname,
                    'phname' => $this->patronymic,
                ])
                ->one();
        }

        return $this->certificate;
    }
}
