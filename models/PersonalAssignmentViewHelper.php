<?php

namespace app\models;

/**
 * Helper for personal assignment
 */
class PersonalAssignmentViewHelper
{
    /**
     * get Nav:widget menu items with url to login under assigned users
     */
    public static function getAssignedUsersNavItems()
    {
        $userIdList = PersonalAssignment::getAssignedUserIdList();
        $userList = User::find()->where(['id' => $userIdList])->all();

        $menuItem = [
            'label' => 'Сменить пользователя',
        ];

        if ($userList) {
            foreach ($userList as $user) {

                $menuItem['items'][] = [
                    'label' => $user->username,
                    'url' => '/personal/assigned-user-login?userId=' . $user->id,
                ];
            }
        }

        return $userList ? $menuItem : '';
    }
}