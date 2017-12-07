<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 06.12.17
 * Time: 11:38
 */

namespace app\helpers;


use yii\base\Model;

class FlashHelper
{
    public static function flashFirst(Model $model, $key = 'warning', $returnResultValue = false)
    {
        \Yii::$app->session->setFlash($key, ModelHelper::getFirstError($model));

        return $returnResultValue;
    }
}
