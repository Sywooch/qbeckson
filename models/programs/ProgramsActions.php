<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 06.12.17
 * Time: 10:13
 */

namespace app\models\programs;


use app\components\SingleModelActions;

abstract class ProgramsActions extends SingleModelActions
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
     * @param ProgrammeModule||integer $certificate
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
