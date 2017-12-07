<?php

namespace app\models\module;

use app\components\SingleModelActions;
use app\helpers\ArrayHelper;
use app\models\ProgrammeModule;
use app\models\UserIdentity;
use yii;
use yii\base\InvalidParamException;

/**
 * Class ModuleActions
 * @package app\models\module
 *
 * @property string $firstErrorAsString
 * @property ProgrammeModule $module
 */
abstract class ModuleActions extends SingleModelActions
{
    /** класс модели над которой производятся действия */
    public static function getTargetModelClass(): string
    {
        return ProgrammeModule::className();
    }


    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'module' => 'Модуль программы',
        ]);
    }

    /**
     * @return yii\db\ActiveRecord|null
     */
    public function getModule()
    {
        return $this->targetModel;
    }

    /**
     *
     * @param ProgrammeModule||integer $module
     *
     * @throws InvalidParamException
     *
     */
    public function setModule($module)
    {
        return parent::setTargetModel($module);
    }

    /**
     * @param bool $validate
     *
     * @return bool
     * @throws yii\db\Exception
     * @throws yii\web\ForbiddenHttpException
     */
    public function save($validate = true)
    {
        if (!$this->isPossibleToSaveTheCurrentUser()) {
            $this->throwForbidden();
        }

        return parent::save($validate);
    }

    /**
     * @return bool
     */
    public function isPossibleToSaveTheCurrentUser()
    {
        return Yii::$app->user->can(UserIdentity::ROLE_OPERATOR);
    }

    public function throwForbidden()
    {
        throw new yii\web\ForbiddenHttpException('Вам запрещено данное действие!!!');
    }


}
