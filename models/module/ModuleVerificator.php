<?php

namespace app\models\module;

use app\models\Informs;
use app\models\ProgrammeModule;
use app\models\UserIdentity;


/**
 * Class ModuleVerificator
 * @package app\models\module
 */
class ModuleVerificator extends ModuleActions
{
    private $withInformer = true;

    /**
     * Все манипуляции внутри этой функции происходят в трансзакции, можно прервать трансзакцию из нутри.
     * для успешного завершения вернуть true
     *
     * @param \Closure $transactionTerminator
     * @param bool $validate
     *
     * @return bool
     */
    public function saveActions(\Closure $transactionTerminator, bool $validate): bool
    {
        return (
                $this->setStateDone()
                && (
                    ($this->withInformer
                        && $this->createInformer())
                    || !$this->withInformer
                )
            )
            || $transactionTerminator();
    }

    private function setStateDone()
    {
        $this->module->verification = ProgrammeModule::VERIFICATION_DONE;

        return true;
    }

    private function createInformer()
    {
        $informs = new Informs();
        $informs->program_id = $this->module->program->id;
        $informs->prof_id = $this->module->program->organization_id;
        $informs->text = 'Сертифицирован модуль(' . $this->module->name . ') программы';
        $informs->from = UserIdentity::ROLE_OPERATOR_ID;
        $informs->date = date("Y-m-d");
        $informs->read = 0;

        return $informs->save();
    }

    public function setWithOutInformer(): self
    {
        $this->withInformer = false;

        return $this;
    }

    public function setWithInformer(): self
    {
        $this->withInformer = true;

        return $this;
    }

}
