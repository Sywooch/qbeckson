<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $access_token
 * @property string $auth_key
 * @property integer $mun_id
 *
 * @property null|\yii\db\ActiveQuery|\app\models\Mun $municipality
 * @property Organization[] $organizations
 */
class User extends ActiveRecord
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
            [['username'], 'string', 'length' => [2, 10]],
            [['confirm', 'oldpassword', 'newpassword'], 'string', 'max' => 10, 'min' => 6],
            [['password', 'access_token', 'auth_key'], 'string', 'max' => 64, 'min' => 6],
            [['newlogin', 'newpass'], 'boolean'],
            [
                'mun_id', 'exist', 'skipOnError' => true,
                'targetClass' => Mun::class,
                'targetAttribute' => ['mun_id' => 'id']
            ],
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
     * @return \yii\db\ActiveQuery|null|Organization[]
     */
    public function getOrganizations()
    {
        return $this->hasMany(Organization::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery|null|Mun
     */
    public function getMunicipality()
    {
        return $this->hasOne(Mun::class, ['id' => 'mun_id']);
    }
}
