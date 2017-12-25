<?php


namespace app\models\programs;


use app\components\SingleModelActions;
use app\helpers\ArrayHelper;
use app\models\Programs;
use yii;
use yii\base\InvalidParamException;

/**
 * Class ProgramsActions
 * @package app\models\programs
 * @property string $firstErrorAsString
 * @property Programs $program
 */
abstract class ProgramsActions extends SingleModelActions
{
    /** класс модели над которой производятся действия */
    public static function getTargetModelClass(): string
    {
        return Programs::className();
    }


    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'program' => 'Программа',
        ]);
    }

    /**
     * @return yii\db\ActiveRecord|null
     */
    public function getProgram()
    {
        return $this->targetModel;
    }

    /**
     *
     * @param Programs||integer $program
     *
     * @throws InvalidParamException
     *
     */
    public function setProgram($program)
    {
        return parent::setTargetModel($program);
    }

}
