<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * уведомление пользователя
 *
 * @property int    $id
 * @property int    $user_id идентификатор пользователя
 * @property string $message текст уведомления
 */
class Notification extends ActiveRecord
{
    /** @inheritdoc */
    public static function tableName()
    {
        return 'notification';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['user_id', 'message'], 'required'],
            ['user_id', 'exist', 'targetClass' => User::className(), 'targetAttribute' => 'id'],
            ['message', 'string'],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'message' => 'текст уведомления',
        ];
    }
}