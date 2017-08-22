<?php

namespace app\traits;

use Yii;
use yii\base\Model;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class AjaxValidationTrait
 * @package common\traits
 */
trait AjaxValidationTrait
{
    /**
     * Performs ajax validation.
     * @param Model $model
     */
    protected function performAjaxValidation(Model $model)
    {
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = ActiveForm::validate($model);
            Yii::$app->response->send();
            Yii::$app->end();
        }
    }
}
