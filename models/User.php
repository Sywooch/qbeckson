<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $access_token
 * @property string $auth_key
 *
 * @property Organization[] $organizations
 */
class User extends \yii\db\ActiveRecord
{
    public $confirm;
    public $oldpassword;
    public $newpassword;
    public $newpass;
    public $newlogin;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'unique'],
            [['username'], 'required'],
            [['username'], 'string', 'max' => 10, 'min' => 3],
            [['confirm', 'oldpassword', 'newpassword'], 'string', 'max' => 10, 'min' => 6],
            [['password', 'access_token', 'auth_key'], 'string', 'max' => 64, 'min' => 6],
            [['newlogin', 'newpass'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'access_token' => 'Access Token',
            'auth_key' => 'Auth Key',
            'newlogin' => 'Изменить логин',
            'newpass' => 'Изменить пароль',
            'oldpassword' => 'Старый пароль',
            'newpassword' => 'Новый пароль',
            'confirm' => 'Подтвердите пароль',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizations()
    {
        return $this->hasMany(Organization::className(), ['user_id' => 'id']);
    }
}
