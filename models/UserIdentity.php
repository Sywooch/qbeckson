<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use developeruz\db_rbac\interfaces\UserRbacInterface;
use yii\web\BadRequestHttpException;
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
 * @property Organization $organization
 * @property null|Certificates $certificate
 * @property null|Mun $municipality
 * @property mixed $userName
 * @property Payers $payer
 * @property \yii\db\ActiveQuery $operator
 */
class UserIdentity extends ActiveRecord implements IdentityInterface, UserRbacInterface
{
    const ROLE_ADMINISTRATOR = 'admins';
    const ROLE_CERTIFICATE = 'certificate';
    const ROLE_PAYER = 'payer';
    const ROLE_ORGANIZATION = 'organizations';
    const ROLE_OPERATOR = 'operators';
    const ROLE_MONITOR = 'monitor';

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
    public function rules()
    {
        return [
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
        return $this->hasOne(Certificates::class, ['user_id' => 'id']);
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
        return $this->hasOne(Operators::class, ['user_id' => 'id']);
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserMonitorAssignment()
    {
        return $this->hasOne(UserMonitorAssignment::className(), ['monitor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonitors()
    {
        return $this->hasMany(UserIdentity::className(), ['id' => 'monitor_id'])->viaTable('user_monitor_assignment', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDonor()
    {
        return $this->hasOne(UserIdentity::className(), ['id' => 'user_id'])->viaTable('user_monitor_assignment', ['monitor_id' => 'id']);
    }

    public function getIsMonitored()
    {
        $monitor = Yii::$app->session->get('user.monitor');

        return (isset($monitor) && $monitor instanceof UserIdentity) ? true : false;
    }

    public function getMonitor()
    {
        $monitor = Yii::$app->session->get('user.monitor');

        if (isset($monitor) && $monitor instanceof UserIdentity) {
            return $monitor;
        }

        throw new BadRequestHttpException('Ошибка обращения к монитору.');
    }

    /**
     * @param $tableName
     * @param null $type
     * @return array|\yii\db\ActiveQuery|ActiveRecord
     */
    public function getFilterSettings($tableName, $type)
    {
        $query = UserSearchFiltersAssignment::find()
            ->joinWith(['filter'])
            ->andWhere([
                'user_search_filters_assignment.user_id' => Yii::$app->user->id,
                'settings_search_filters.table_name' => $tableName,
                'settings_search_filters.type' => $type
            ]);

        return $query->one();
    }

    /**
     * @return array
     */
    public static function roles()
    {
        return [
            self::ROLE_ADMINISTRATOR => 'Администратор',
            self::ROLE_CERTIFICATE => 'Сертификат',
            self::ROLE_PAYER => 'Плательщик',
            self::ROLE_ORGANIZATION => 'Организация',
            self::ROLE_OPERATOR => 'Оператор',
            self::ROLE_MONITOR => 'Монитор',
        ];
    }
}
