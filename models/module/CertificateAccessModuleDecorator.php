<?php

namespace app\models\module;


use app\components\ModelDecorator;
use app\models\Certificates;
use app\models\Cooperate;
use app\models\ProgrammeModule;
use app\models\UserIdentity;

/**
 * Class CertificateAccess
 * @package app\models\module
 * @property ProgrammeModule $entity
 * @mixin ProgrammeModule
 */
class CertificateAccessModuleDecorator extends ModelDecorator
{
    private static $cooperateErr = false;
    private $lastMessage;

    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    public function setCertificate(Certificates $certificate)
    {
        $this->certificate = $certificate;
    }

    public function setModule(ProgrammeModule $module)
    {
        $this->module = $module;
    }

    public function certificateCanEnlistmentToProgram(): bool
    {
        return $this->haveCooperate()
            && $this->moduleIsOpen()
            && $this->moduleHaveGroups()
            && $this->certificateHaveMoney()
            && $this->organizationIsActual()
            && $this->havePlaceInActiveContractsByProgramInPayer()
            && $this->haveFreePlaceInOrganization()
            && $this->haveFreePlaceInProgram()
            && $this->notHaveActiveContractsInThisProgramm()
            && (
                $this->pushMessage('Вы можете записаться на программу. Выберете группу:')
                || true
            );
    }

    public function haveCooperate(): bool
    {
        return Cooperate::find()->where([
                Cooperate::tableName() . '.[[payer_id]]' => $this->getUser()
                    ->getCertificate()->select('payer_id'),
                Cooperate::tableName() . '.[[organization_id]]' => $this->getCertificate()
                    ->organization_id,
                'status' => Cooperate::STATUS_ACTIVE])->exists()
            || $this->pushMessage(
                'К сожалению, на данный момент Вы не можете записаться на '
                . 'обучение в организацию, реализующую выбранную программу. '
                . 'Уполномоченная организация пока не заключила с ней необходимое соглашение.'
            )
            || $this->emitCooperateFlash();
    }

    public function getUser(): UserIdentity
    {
        /** @var $user UserIdentity */
        $user = \Yii::$app->user->identity;

        return $user;
    }

    public function getCertificate(): Certificates
    {
        return \Yii::$app->user->identity->certificate;
    }

    private function pushMessage(string $message): bool
    {
        $this->lastMessage = $message;

        return false;
    }

    public function emitCooperateFlash(): bool
    {
        if (!self::$cooperateErr) {
            \Yii::$app->session->setFlash(
                'warning',
                'К сожалению, на данный момент Вы не можете записаться на '
                . 'обучение в организацию, реализующую выбранную программу. '
                . 'Уполномоченная организация пока не заключила с ней необходимое соглашение.'
            );
            self::$cooperateErr = true;
        }

        return false;
    }

    public function moduleIsOpen(): bool
    {
        return $this->getModule()->open
            || $this->pushMessage('Вы не можете записаться на программу. Зачисление закрыто.');
    }

    public function getModule(): ProgrammeModule
    {
        return $this->entity;
    }

    public function moduleHaveGroups(): bool
    {
        return $this->getModule()->getGroups()->exists()
            || $this->pushMessage('Нет доступных групп в которые можно записаться.');
    }

    public function certificateHaveMoney(): bool
    {
        return (
                !(/**отрицание - зло, но честно говоря боюсь трогать эту логику*/
                    $this->getCertificate()->balance < 1
                    && $this->getCertificate()->payer->certificate_can_use_future_balance < 1)
                || ($this->getCertificate()->balance < 1
                    && $this->getCertificate()->payer->certificate_can_use_future_balance > 0
                    && $this->getCertificate()->balance_f < 1
                )
            )
            || $this->pushMessage('Вы не можете записаться на программу. '
                . 'Нет свободных средств на сертификате.');
    }

    public function organizationIsActual(): bool
    {
        return $this->getModule()->program->organization->actual
            || $this->pushMessage('Вы не можете записаться на программу. '
                . 'Действие организации приостановленно.');
    }

    public function havePlaceInActiveContractsByProgramInPayer(): bool
    {
        $payer = $this->getCertificate()->payer;
        $program = $this->getModule()->program;
        $activeContractsByProgramInPayer = $payer
            ->getActiveContractsByProgram($program->id)
            ->count();
        $limit_napr = $payer->getDirectionalityCountByName($program->directivity);

        return ($activeContractsByProgramInPayer < $limit_napr)
            || $this->pushMessage('Вы не можете записаться на программу. '
                . 'Достигнут максимальный предел числа одновременно '
                . 'оплачиваемых вашей уполномоченной организацией услуг '
                . 'по данной направленности.');
    }

    public function haveFreePlaceInOrganization(): bool
    {
        return $this->getModule()->program->organization->existsFreePlace()
            || $this->pushMessage('Вы не можете записаться на программу. '
                . 'Достигнут максимальный лимит зачисления в организацию. '
                . 'Свяжитесь с представителем организации.');
    }

    public function haveFreePlaceInProgram(): bool
    {
        return $this->getModule()->program->existsFreePlace()
            || $this->pushMessage('Достигнут максимальный лимит зачисления '
                . 'на обучение по программе. Свяжитесь с представителем организации.');
    }

    public function notHaveActiveContractsInThisProgramm()
    {
        return !($this->getCertificate()
                ->getActiveContractsByProgram(
                    $this->getModule()->program_id
                )
                ->exists())
            || $this->pushMessage('Вы уже подали заявку на программу/заключили договор на обучение');
    }

}
