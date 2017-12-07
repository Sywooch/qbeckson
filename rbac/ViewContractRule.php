<?php

namespace app\rbac;

use app\models\Contracts;
use app\models\UserIdentity;
use Yii;
use yii\rbac\Item;
use yii\rbac\Rule;

class ViewContractRule extends Rule
{
    public $name = 'canViewContract';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     *
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if (!isset($params['id']) || !$contract = Contracts::findOne($params['id'])) {
            return false;
        }
        /**@var $userIdentity UserIdentity */
        $userIdentity = Yii::$app->user->identity;

        if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)
            && $contract->certificate_id == $userIdentity->certificate->id
        ) {
            return true;
        }
        if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)
            && $contract->organization_id == $userIdentity->organization->id
        ) {
            return true;
        }
        if (Yii::$app->user->can(UserIdentity::ROLE_PAYER)
            && $contract->payer_id == $userIdentity->payer->id
        ) {
            return true;
        }
        if (Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)
            && $contract->payer->operator_id == $userIdentity->operator->id
        ) {
            return true;
        }

        return false;
    }
}
