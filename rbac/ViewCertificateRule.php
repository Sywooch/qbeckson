<?php

namespace app\rbac;

use app\models\Certificates;
use app\models\Contracts;
use app\models\UserIdentity;
use Yii;
use yii\rbac\Rule;

class ViewCertificateRule extends Rule
{
    public $name = 'canViewCertificate';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if (!isset($params['id']) || !$certificate = Certificates::findOne($params['id'])) {
            return false;
        }

        $userIdentity = Yii::$app->user->identity;

        if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            if (Contracts::findByCertificate($certificate->id) > 0) {
                return true;
            }
        }
        if (Yii::$app->user->can(UserIdentity::ROLE_PAYER) && $certificate->payer_id == $userIdentity->payer->id) {
            return true;
        }
        if (Yii::$app->user->can(UserIdentity::ROLE_OPERATOR) && $certificate->payer->operator_id == $userIdentity->operator->id) {
            return true;
        }

        return false;
    }
}