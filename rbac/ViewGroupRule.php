<?php

namespace app\rbac;

use Yii;
use app\models\Groups;
use app\models\UserIdentity;
use yii\rbac\Rule;

class ViewGroupRule extends Rule
{
    public $name = 'canViewGroup';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if (!isset($params['id']) || !$group = Groups::findOne($params['id'])) {
            return false;
        }

        if (Yii::$app->user->can('viewProgramme', ['id' => $group->program_id])) {
            return true;
        }

        return false;
    }
}