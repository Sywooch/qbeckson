<?php

namespace app\controllers;

use Yii;
use app\models\Import;
use yii\web\UploadedFile;

class ImportController extends \yii\web\Controller
{
    public function actionChildren()
    {
        $model = new Import();

        if (Yii::$app->request->isPost) {
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            if ($model->insertChildrenFromCsv()) {
                Yii::$app->session->setFlash('success', 'Файл успешно импортирован.');

                return $this->refresh();
            }
        }

        return $this->render('children', [
            'model' => $model,
        ]);
    }

    public function actionChildrenPassword()
    {
        $model = new Import();

        if (Yii::$app->request->isPost) {
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            if ($model->updateChildrenPassword()) {
                Yii::$app->session->setFlash('success', 'Пароли успешно изменены.');

                return $this->refresh();
            }
        }

        return $this->render('children-password', [
            'model' => $model,
        ]);
    }
}
