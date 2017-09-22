<?php

namespace app\controllers\guest;

use app\models\Organization;
use app\models\Programs;
use app\models\search\OrganizationSearch;
use app\models\search\ProgramsSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class GeneralController
 * @package app\controllers\guest
 */
class GeneralController extends Controller
{
    /**
     * @return string
     */
    public function actionPrograms(): string
    {
        $model = new ProgramsSearch([
            'verification' => [Programs::VERIFICATION_DONE],
            'modelName'    => '',
        ]);
        $provider = $model->search(Yii::$app->request->queryParams);

        return $this->render('programs', [
            'model' => $model,
            'provider' => $provider,
        ]);
    }

    /**
     * @param integer $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionProgram($id): string
    {
        return $this->render('program', [
            'model' => $this->findProgram($id)
        ]);
    }

    /**
     * @return string
     */
    public function actionOrganizations(): string
    {
        $model = new OrganizationSearch([
            'modelName' => '',
            'statusArray' => Organization::STATUS_ACTIVE,
        ]);
        $provider = $model->search(Yii::$app->request->queryParams);

        return $this->render('organizations', [
            'model' => $model,
            'provider' => $provider,
        ]);
    }

    /**
     * @param integer $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionOrganization($id): string
    {
        return $this->render('organization', [
            'model' => $this->findOrganization($id)
        ]);
    }

    /**
     * Finds the Programs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Programs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findProgram($id)
    {
        if (($model = Programs::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Программа не существует');
        }
    }

    /**
     * Finds the Organization model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Organization the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findOrganization($id)
    {
        if (($model = Organization::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Организация не сущетвует');
        }
    }
}
