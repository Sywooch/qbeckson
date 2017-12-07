<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 06.12.17
 * Time: 10:13
 */

namespace app\models\programs;

use app\helpers\ModelHelper;
use app\models\module\ModuleNormativePriceCalculator;


/**
 * Class ProgramsNormativePriceCalculator
 * @package app\models\programs
 */
class ProgramsNormativePriceCalculator extends ProgramsActions
{
    private function calcNormativeIntoModules()
    {
        foreach ($this->program->modules as $module) {
            $calculator = new ModuleNormativePriceCalculator($module);

            if (!$calculator->save()) {
                $this->addError('program', ModelHelper::getFirstError($calculator));

                return false;
            }
        }

        return true;
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
        return $this->calcNormativeIntoModules() || $transactionTerminator();
    }

}
