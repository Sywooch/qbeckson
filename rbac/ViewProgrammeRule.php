<?php

namespace app\rbac;

use app\models\Programs;
use app\models\UserIdentity;
use Yii;
use yii\rbac\Rule;

class ViewProgrammeRule extends Rule
{
    public $name = 'canViewProgramme';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if (!isset($params['id']) || !$programme = Programs::findOne($params['id'])) {
            return false;
        }

        $userIdentity = Yii::$app->user->identity;

        if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE) && $programme->municipality->operator_id == $userIdentity->certificate->payer->operator_id) {
            return true;
        }
        if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION) && $programme->organization_id == $userIdentity->organization->id) {
            return true;
        }
        if (Yii::$app->user->can(UserIdentity::ROLE_PAYER)) {
            $organizations = Yii::$app->user->identity->payer->findCooperateOrganizations();

            if (in_array($programme->organization_id, $organizations)) {
                return true;
            }
        }
        if (Yii::$app->user->can(UserIdentity::ROLE_OPERATOR) && $programme->municipality->operator_id == $userIdentity->operator->id) {
            return true;
        }

        return false;
    }
}