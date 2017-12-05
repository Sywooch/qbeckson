<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 30.11.17
 * Time: 19:06
 */

namespace app\components;


use app\models\UserIdentity;
use yii\i18n\Formatter;

class AppFormatter extends Formatter
{
    public function asUserName($userId)
    {
        $identity = UserIdentity::findIdentity($userId);
        if (!$identity) {
            return 'не найден';
        } else {
            return $identity->userName;
        }
    }
}
