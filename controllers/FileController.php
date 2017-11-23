<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;

/**
 * Controller for file response
 */
class FileController extends Controller
{
    /**
     * @param $path - path to contract
     *
     * @return Response
     */
    public function actionContract($path)
    {
        return \Yii::$app->response->sendFile(\Yii::getAlias('@pfdoroot') . $path);
    }
}