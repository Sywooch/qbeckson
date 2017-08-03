<?php

namespace app\controllers;

use app\models\UserIdentity;
use Yii;
use app\models\CertificateInformation;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class CertificateInformationController
 * @package app\controllers
 */
class CertificateInformationController extends Controller
{
    /**
     * @return mixed
     */
    public function actionUpdate()
    {
        if (false === ($model = $this->findModel())) {
            $model = new CertificateInformation();
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['personal/payer-statistic']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @return CertificateInformation|false the loaded model
     */
    protected function findModel()
    {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->getIdentity();
        if (($model = $user->payer->certificateInformation) !== null) {
            return $model;
        }

        return false;
    }
}
