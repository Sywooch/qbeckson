<?php

namespace app\models;

use Yii;
use app\models\UserIdentity;
use yii\base\Model;

/**
 * Class UserCreateForm
 * @package app\forms
 */
class UserForm extends Model
{
    public $password;
    public $username;

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
        ];
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
