<?php

namespace app\controllers\guest;

use app\models\Organization;
use app\models\Programs;
use app\models\search\OrganizationSearch;
use app\models\search\ProgramsSearch;
use Yii;
use yii\web\Controller;

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
            'verification' => [2],
            'modelName' => '',
        ]);
        $provider = $model->search(Yii::$app->request->queryParams);

        return $this->render('programs', [
            'model' => $model,
            'provider' => $provider,
        ]);
    }

    /**
     * @param integer $id
     * @return string
     */
    public function actionProgram($id): string
    {
        return $this->render('program', [
            'model' => Programs::findOne($id)
        ]);
    }

    /**
     * @return string
     */
    public function actionOrganizations(): string
    {
        $model = new OrganizationSearch([
            'modelName' => ''
        ]);
        $provider = $model->search(Yii::$app->request->queryParams);

        return $this->render('organizations', [
            'model' => $model,
            'provider' => $provider,
        ]);
    }

    /**
     * @param integer $id
     * @return string
     */
    public function actionOrganization($id): string
    {
        return $this->render('organization', [
            'model' => Organization::findOne($id)
        ]);
    }
}
