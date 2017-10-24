<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class UserPersonalAssign
 * @property int $id             [int(11)]
 * @property int $user_id        [int(11)]  id пользователя
 * @property int $assign_user_id [int(11)]  id связанного пользователя
 */
class UserPersonalAssign extends ActiveRecord
{
    /** @inheritdoc */
    public static function tableName()
    {
        return 'user_personal_assign';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['user_id', 'assign_user_id'], 'exist', 'targetClass' => User::className(), 'targetAttribute' => 'id'],
            ['user_id', 'compare', 'operator' => '!=', 'compareAttribute' => 'assign_user_id'],
            [['user_id', 'assign_user_id'], 'unique', 'targetAttribute' => ['user_id', 'assign_user_id']],
            [['user_id', 'assign_user_id'], 'assignExistValidator', 'params' => 'user_id'],
        ];
    }

    /**
     * Validate attributes for assigning already assigned user ids
     */
    public function assignExistValidator()
    {
        if (in_array($this->assign_user_id, PersonalAssignment::getAssignedUserIdList($this->user_id))) {
            $this->addError('user_id', 'Указанные идентификаторы пользотвателей уже связаны');
        }
    }
}