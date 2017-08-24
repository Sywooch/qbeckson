<?php

namespace app\models\forms;

use app\models\Cooperate;
use app\models\UserIdentity;
use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;

/**
 * Class RejectContractForm
 * @package app\models\forms
 */
class RejectContractForm extends Model
{
    private $model;

    /**
     * RejectContractForm constructor.
     * @param integer $contractId
     * @param array $config
     */
    public function __construct($contractId, $config = [])
    {
        $this->setModel($contractId);
        parent::__construct($config);
    }

    /**
     * @return bool|false|int
     */
    public function reject()
    {
        if (null !== ($model = $this->getModel()) &&
            count($model->contracts) < 1
        ) {
            return $model->delete();
        }

        return false;
    }

    /**
     * @return Cooperate
     */
    public function getModel(): Cooperate
    {
        return $this->model;
    }

    /**
     * @param integer $contractId
     * @throws NotFoundHttpException
     */
    public function setModel($contractId)
    {
        $user = Yii::$app->user;
        if ($user->can(UserIdentity::ROLE_PAYER)) {
            $this->model = Cooperate::findOne([
                'id' => $contractId,
                'payer_id' => $user->getIdentity()->payer->id,
            ]);
        }
        if ($user->can(UserIdentity::ROLE_ORGANIZATION)) {
            $this->model = Cooperate::findOne([
                'id' => $contractId,
                'organization_id' => $user->getIdentity()->organization->id,
            ]);
        }
        if (null === $this->model) {
            throw new NotFoundHttpException('Model not found');
        }
    }
}
