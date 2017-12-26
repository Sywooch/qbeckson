<?php

namespace app\models\module;


use app\models\Certificates;
use app\models\Cooperate;
use app\models\ProgrammeModule;
use app\models\UserIdentity;

/**
 * Class CertificateAccess
 * @package app\models\module
 * @property ProgrammeModule $entity
 */
class CertificateAccess
{
    private $certificate;
    private $module;

    public function __construct(Certificates $certificate, ProgrammeModule $module)
    {
        $this->certificate = $certificate;
        $this->module = $module;
    }

    public static function instanceByProgram(Certificates $certificate, ProgrammeModule $module): self
    {
        return new self($certificate, $module);
    }

    public function haveCooperate(): bool
    {
        return Cooperate::find()->where([
            Cooperate::tableName() . '.[[payer_id]]' => $this->getUser()
                ->getCertificate()->select('payer_id'),
            Cooperate::tableName() . '.[[organization_id]]' => $this->getCertificate()
                ->organization_id,
            'status' => Cooperate::STATUS_ACTIVE])->exists();
    }

    public function getUser(): UserIdentity
    {
        /** @var $user UserIdentity */
        $user = \Yii::$app->user->identity;

        return $user;
    }

    public function getCertificate(): Certificates
    {
        return $this->certificate;
    }

    public function setCertificate(Certificates $certificate)
    {
        $this->certificate = $certificate;
    }

    public function moduleIsOpen(): bool
    {
        return $this->getModule()->open;
    }

    public function getModule(): ProgrammeModule
    {
        return $this->module;
    }

    public function setModule(ProgrammeModule $module)
    {
        $this->module = $module;
    }

    public function moduleHaveGroups(): bool
    {
        return $this->getModule()->getGroups()->exists();
    }

    public function certificateHaveMoney(): bool
    {
        return !(/**отрицание - зло, но честно говоря боюсь трогать эту логику*/
                $this->getCertificate()->balance < 1
                && $this->getCertificate()->payer->certificate_can_use_future_balance < 1)
            || ($this->getCertificate()->balance < 1
                && $this->getCertificate()->payer->certificate_can_use_future_balance > 0
                && $this->getCertificate()->balance_f < 1
            );
    }

    public function organizationIsActual(): bool
    {
        return $this->getModule()->program->organization->actual;
    }

    public function havePlaceInActiveContractsByProgramInPayer(): bool
    {
        $payer = $this->getCertificate()->payer;
        $program = $this->getModule()->program;
        $activeContractsByProgramInPayer = $payer
            ->getActiveContractsByProgram($program->id)
            ->count();
        $limit_napr = $payer->getDirectionalityCountByName($program->directivity);

        return $activeContractsByProgramInPayer >= $limit_napr;
    }

    public function haveFreePlaceInOrganization(): bool
    {
        return $this->getModule()->program->organization->existsFreePlace();
    }

    public function haveFreePlaceInProgram(): bool
    {
        return $this->getModule()->program->existsFreePlace();
    }


}
