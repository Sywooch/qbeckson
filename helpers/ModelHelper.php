<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 05.12.17
 * Time: 12:52
 */

namespace app\helpers;


use yii\base\Model;

class ModelHelper
{
    public static function getFirstError(Model $model)
    {
        $errs = $model->getFirstErrors();

        return reset($errs);
    }
}
