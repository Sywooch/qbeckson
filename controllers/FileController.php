<?php

namespace app\controllers;

use app\models\ContractDeleteApplication;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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

    /**
     * @param $id ContractDeleteApplication::id
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionContractDeleteDocument($id)
    {
        $model = ContractDeleteApplication::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Страница не найдена.');
        }

        $file = \Yii::getAlias('@pfdoroot') . $model::FILE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $model->file;

        if (file_exists($file)) {
            return \Yii::$app->response->sendFile($file);
        } else {
            throw new NotFoundHttpException('Страница не найдена.');
        }
    }
}