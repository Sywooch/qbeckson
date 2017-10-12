<?php

namespace app\models;

use app\helpers\PermissionHelper;
use Yii;
use app\models\UserIdentity;
use yii\base\Model;

/**
 * Class UserForm
 * @package app\forms
 */
class UserForm extends Model
{
    public $password;
    public $username;
    public $status;
    public $blockReason;
    public $accessRights;

    private $model;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'username'], 'trim'],
            ['password', 'string', 'min' => 6, 'max' => 64],
            ['username', 'string', 'min' => 3, 'max' => 64],
            [
                'username', 'unique', 'targetClass' => UserIdentity::class, 'filter' => function ($query) {
                    if (!$this->getModel()->isNewRecord) {
                        /** @var \yii\db\Query $query */
                        $query->andWhere(['not', ['id' => $this->getModel()->id]]);
                    }
                }
            ],
            ['status', 'in', 'range' => array_keys(UserIdentity::statuses())],
            ['blockReason', 'string'],
            ['accessRights', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'status' => 'Текущий статус',
            'blockReason' => 'Причина блокировки',
            'accessRights' => 'Права доступа',
        ];
    }

    public function getRightsList()
    {
        return PermissionHelper::getAccessUrls();
    }

    /**
     * Create user method.
     *
     * @return User|null
     */
    public function create()
    {
        if ($this->validate() && ($user = $this->getModel())) {
            $security = Yii::$app->getSecurity();
            $user->username = $this->username ?: $security->generateRandomString(10);
            if (!$this->password) {
                $this->password = $security->generateRandomString($length = 10);
            }
            $user->setPassword($this->password);

            return $user->save(false) ? $user : null;
        }

        return null;
    }

    /**
     * Confirm user method.
     *
     * @return User|null
     */
    public function confirm()
    {
        if ($this->validate() && ($user = $this->getModel())) {
            $security = Yii::$app->getSecurity();
            if ($this->username) {
                $user->username = $this->username;
            }
            if (!$this->password) {
                $this->password = $security->generateRandomString($length = 10);
            }
            $user->setPassword($this->password);

            return $user->save(false) ? $user : null;
        }

        return null;
    }

    /**
     * Change user method.
     *
     * @return User|null
     */
    public function save()
    {
        if ($this->validate() && ($user = $this->getModel())) {
            $user->username = $this->username;
            $user->status = $this->status;
            $user->block_reason = $this->blockReason;
            if ($this->password) {
                $user->setPassword($this->password);
            }

            return $user->save(false) ? $user : null;
        }

        return null;
    }

    /**
     * @param User $model
     */
    public function setModel($model)
    {
        $this->model = $model;
        $this->username = $model->username;
        $this->status = $model->status;
        $this->blockReason = $model->block_reason;
        $this->accessRights = $model->userMonitorAssignment->access_rights;
    }

    /**
     * @return User
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new UserIdentity();
        }

        return $this->model;
    }
}
