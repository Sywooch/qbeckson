<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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

    const SCENARIO_SHORT_LOGIN = 'short_login';

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
            [['username'], 'unique', 'on' => self::SCENARIO_DEFAULT ],
            [['username'], 'required'],
            [['confirm'], 'required'],
            [['confirm'], 'validatePassword', 'on' => self::SCENARIO_SHORT_LOGIN],
            [['username'], 'string', 'length' => [2, 10]],
            [['confirm', 'oldpassword', 'newpassword'], 'string', 'max' => 10, 'min' => 6],
            [['password', 'access_token', 'auth_key'], 'string', 'max' => 64, 'min' => 6],
            [['newlogin', 'newpass'], 'boolean'],
            ['mun_id', 'integer'],
        ];
    }


    public function validatePassword($attribute)
    {
        \Yii::$app->getSecurity()->validatePassword($this->{$attribute}, $this->password) ||
            $this->addError($attribute, 'Не правильно введен пароль');
    }

    /**
     * @return $this
     */
    public function setShortLoginScenario()
    {
        $this->scenario = self::SCENARIO_SHORT_LOGIN;
        return $this;
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

    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(),
            [self::SCENARIO_SHORT_LOGIN => ['username', 'password']]
        );
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
