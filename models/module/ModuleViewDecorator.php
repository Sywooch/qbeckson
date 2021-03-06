<?php

namespace app\models\module;


use app\components\ModelDecorator;
use app\models\Contracts;
use app\models\Organization;
use app\models\ProgrammeModule;
use app\models\Programs;
use Yii;
use yii\helpers\Url;

/**
 * @property ProgrammeModule $entity
 * ***********************
 * @mixin ProgrammeModule
 *
 */
class ModuleViewDecorator extends ModelDecorator
{
    private $localOrganisation;

    public function canEditPrice(): bool
    {
        return $this->havePrice()
            && !$this->open
            && !$this->entity->canChangePrice();
    }

    public function havePrice(): bool
    {
        return $this->price > 0;
    }

    public function isVerificate(): bool
    {
        return $this->programIsVerificate() && $this->moduleIsVerificate();
    }

    public function programIsVerificate(): bool
    {
        return $this->program->verification === Programs::VERIFICATION_DONE;
    }

    public function moduleIsVerificate(): bool
    {
        return $this->verification === ProgrammeModule::VERIFICATION_DONE;
    }

    public function haveAccessToEnlistment(): bool
    {
        return (
            $this->haveAccess()
            && $this->organisationIsActual()
            && $this->havePrice()
            && (
                $this->isOrganisationWithoutWorkers()
                || (
                    $this->orgranisationInfoIsFill()
                    && (
                        (
                            $this->organisationDocTypeIsProxy()
                            && $this->proxyOrganisationInfoIsFill()
                        )
                        || (
                        !$this->organisationDocTypeIsProxy()
                        )
                    )
                )
            )
        );
    }

    public function haveAccess(): bool
    {
        return $this->havePermissions()
            && $this->haveAccessModule()
            && $this->haveAccessProgram();
    }

    public function havePermissions()
    {
        return Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION);
    }

    public function haveAccessModule(): bool
    {
        return ($this->entity->verification !== \app\models\ProgrammeModule::VERIFICATION_DENIED
            && $this->entity->verification !== \app\models\ProgrammeModule::VERIFICATION_WAIT);
    }

    public function haveAccessProgram(): bool
    {
        return ($this->program->verification !== Programs::VERIFICATION_DENIED
            && $this->program->verification !== Programs::VERIFICATION_WAIT
        );
    }

    public function organisationIsActual(): bool
    {
        return $this->getLocalOrganisation()->actual;
    }

    private function getLocalOrganisation(): Organization
    {
        if ($this->localOrganisation) {
            return $this->localOrganisation;
        } else {
            $this->localOrganisation = Yii::$app->user->identity->organization;

            return $this->localOrganisation;
        }
    }

    public function isOrganisationWithoutWorkers(): bool
    {
        return $this->entity->program->organization->type
            == \app\models\Organization::TYPE_IP_WITHOUT_WORKERS;
    }

    public function orgranisationInfoIsFill(): bool
    {
        return $this->getLocalOrganisation()->license_issued_dat
            && $this->getLocalOrganisation()->fio
            && $this->getLocalOrganisation()->position
            && $this->getLocalOrganisation()->doc_type;
    }

    public function organisationDocTypeIsProxy(): bool
    {
        return $this->getLocalOrganisation()->doc_type === \app\models\Organization::DOC_TYPE_PROXY;
    }

    public function proxyOrganisationInfoIsFill(): bool
    {
        return $this->getLocalOrganisation()->date_proxy
            && $this->getLocalOrganisation()->number_proxy;
    }

    public function getLabelEnlistment()
    {
        return $this->open ? 'Закрыть зачисление' : 'Открыть зачисление';
    }

    public function getEnlistmentActionUrl()
    {
        return $this->open
            ? Url::to(['years/stop', 'id' => $this->id])
            : Url::to(['years/start', 'id' => $this->id]);
    }

    public function getClassButtonEnlistment()
    {
        return $this->open ? 'btn-danger' : 'btn-theme';
    }

    public function getEnlistmentWarning()
    {
        if (!$this->organisationIsActual()) {
            return 'Деятельность приостановлена';
        } elseif (!$this->havePrice()) {
            return 'Нет цены, нельзя открыть';
        } elseif (!$this->orgranisationInfoIsFill()
            || ($this->organisationDocTypeIsProxy()
                && !$this->proxyOrganisationInfoIsFill())
        ) {
            return 'Заполните информацию "Для договора"';
        } elseif (!$this->haveAccess()) {
            return 'Нет доступа, или сертификация в процессе';
        } else {
            return 'Не известная причина';
        }
    }

    public function getNoAccessMessage()
    {
        if (!$this->havePermissions()) {
            return 'Нет прав';
        } elseif (!$this->haveAccessProgram()) {
            return 'Программа ожидает сертификацию или в данной отказано';
        } elseif (!$this->haveAccessModule()) {
            return 'Модуль ожидает сертификацию или в данной отказано';
        } else {
            return 'Не известная причина';
        }
    }
}
