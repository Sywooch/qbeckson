<?php

namespace app\models\module;


class ModuleVerificator extends ModuleActions
{
    public function setStateWait()
    {
        if (!$this->isPossibleToSaveTheCurrentUser()) {
            $this->module->verification = $this->module::VERIFICATION_WAIT;

            return $this->module->save(false);
        }
        $this->throwForbidden();
    }


    public function canVerify()
    {

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
    public function saveActions(\Closure $transactionTerminator, bool $validate): bool
    {
        // TODO: Implement saveActions() method.
    }


}
