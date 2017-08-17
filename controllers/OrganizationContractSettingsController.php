<?php

namespace app\controllers;

use Yii;
use app\models\OrganizationContractSettings;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OrganizationContractSettingsController implements the CRUD actions for OrganizationContractSettings model.
 */
class OrganizationContractSettingsController extends Controller
{


    /**
     * @param integer $id
     * @return OrganizationContractSettings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrganizationContractSettings::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
