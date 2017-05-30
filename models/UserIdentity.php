<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use developeruz\db_rbac\interfaces\UserRbacInterface;
use yii\web\IdentityInterface;

/**
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $access_token
 * @property string $auth_key
 * @property integer $mun_id
 * @property mixed $authKey
 *
 * @property \yii\db\ActiveQuery $dispute
 * @property \yii\db\ActiveQuery $organization
 * @property \yii\db\ActiveQuery $certificate
 * @property null|\yii\db\ActiveQuery|\app\models\Mun $municipality
 * @property mixed $userName
 * @property \yii\db\ActiveQuery $payer
 * @property \yii\db\ActiveQuery $operator
 */
class UserIdentity extends ActiveRecord implements IdentityInterface, UserRbacInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->username;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        //return $this->password === md5($password);
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificate()
    {
        return $this->hasOne(Certificates::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDispute()
    {
        return $this->hasOne(Disputes::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperator()
    {
        return $this->hasOne(Operators::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayer()
    {
        return $this->hasOne(Payers::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery|null|Mun
     */
    public function getMunicipality()
    {
        return $this->hasOne(Mun::class, ['id' => 'mun_id']);
    }
}
