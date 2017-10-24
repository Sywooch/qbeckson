<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class to assign personal area
 */
class PersonalAssignment
{
    /**
     * get list of all assigned mun ids by userId
     *
     * @param null $userId - if null get for current user
     *
     * @return array
     */
    public static function getAssignedMunList($userId = null)
    {
        $result = [];
        foreach (self::getAssignedUserIdList($userId) as $item) {
            $result[] = Payers::findOne(['user_id' => $item])->mun;
        }

        return $result;
    }

    /**
     * get list of all assigned users by userId
     *
     * @param $userId - if null get for current user id
     *
     * @return array
     */
    public static function getAssignedUserIdList($userId = null)
    {
        $assignUserIdList = UserPersonalAssign::find()->select('user_id, assign_user_id')->asArray()->all();

        return self::searchAssign($assignUserIdList, $userId ? $userId : Yii::$app->user->identity->id);
    }

    /**
     * recursive search of assigned user ids
     *
     * @param array $array
     * @param integer $userId
     * @param integer|null $searchId
     *
     * @return array
     */
    private static function searchAssign(&$array, $userId, $searchId = null) {
        $result = [];
        $searchAssign = [];
        $keys = [];
        foreach ($array as $key => $item) {
            if ($item['user_id'] == $userId) {
                $result[] = $item['assign_user_id'];
                $keys[] = $key;

                if ($searchId && $searchId == $item['assign_user_id']) {
                    $searchAssign = [$item['user_id'], $searchId];
                }
            } elseif($item['assign_user_id'] == $userId) {
                $result[] = $item['user_id'];
                $keys[] = $key;

                if ($searchId && $searchId == $item['user_id']) {
                    $searchAssign = [$searchId, $item['assign_user_id']];
                }
            }
        }

        foreach ($keys as $key) {
            unset($array[$key]);
        }

        foreach ($result as $item) {
            foreach (self::searchAssign($array, $item, $searchId) as $search) {
                $result[] = $search;

                if ($searchId) {
                    $searchAssign[] = $search;
                }
            }
        }

        return $searchId ? $searchAssign : array_unique($result);
    }

    /**
     * get ActiveDataProvider mun list for operator of current payer
     *
     * @return ActiveDataProvider
     */
    public static function getDataProviderForMunList()
    {
        $operator = Operators::find()->leftJoin('payers', 'payers.operator_id = operators.id')->where(['payers.user_id' => \Yii::$app->user->identity->id])->one();

        if ($operator) {
            $getMunQuery = $operator->getMun()->leftJoin('payers', 'mun.id = payers.mun')->where(['!=', 'payers.user_id', \Yii::$app->user->identity->id]);
        }

        $activeDataProvider = new ActiveDataProvider;
        $activeDataProvider->query = $getMunQuery;

        return $activeDataProvider;
    }

    /**
     * assign user personal to current user by munId
     *
     * @param $munId
     */
    public static function assignUserPersonalByMunId($munId)
    {
        if ($payers = Payers::findOne(['mun' => $munId])) {
            $userPersonalAssign = new UserPersonalAssign(['user_id' => \Yii::$app->user->identity->id, 'assign_user_id' => $payers->user_id]);
            $userPersonalAssign->save();
        }
    }

    /**
     * assign user personal to current user by munId
     *
     * @param $munId
     */
    public static function removeAssignUserPersonalByMunId($munId)
    {
        $assignUserIdList = UserPersonalAssign::find()->select('user_id, assign_user_id')->asArray()->all();

        if ($payers = Payers::findOne(['mun' => $munId])) {
            $userIdAssignList = self::searchAssign($assignUserIdList, Yii::$app->user->identity->id, $payers->user_id);

            $userPersonalAssign = UserPersonalAssign::findOne(['user_id' => $userIdAssignList[0], 'assign_user_id' => $userIdAssignList[1]]);
            if ($userPersonalAssign) {
                $userPersonalAssign->delete();
            }
        }
    }
}