<?php

namespace app\controllers;

use app\models\certificates\CertificateImportTemplate;
use Yii;
use app\models\Import;
use yii\web\UploadedFile;

class ImportController extends \yii\web\Controller
{
    public function actionUploadCertificateImportTemplate()
    {
        $certificateImportTemplate = CertificateImportTemplate::find()->one();

        if (is_null($certificateImportTemplate)) {
            $certificateImportTemplate = new CertificateImportTemplate;
        }

        if ($certificateImportTemplate->load(\Yii::$app->request->post())) {
            $certificateImportTemplate->save();
        }

        return $this->render('upload-certificate-import-template', [
            'certificateImportTemplate' => $certificateImportTemplate,
        ]);
    }

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
