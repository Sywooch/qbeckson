<?php

namespace app\controllers\organization;

use app\models\ContractDeleteApplication;
use app\models\search\ContractDeleteApplicationSearch;
use app\models\search\ContractsSearch;
use yii\web\Controller;

class CleanupController extends Controller
{
    /**
     * @return string
     */
    public function actionContract()
    {
        $waitingModel = new ContractDeleteApplicationSearch([
            'status' => ContractDeleteApplication::STATUS_WAITING,
        ]);
        $confirmedModel = new ContractDeleteApplicationSearch([
            'status' => ContractDeleteApplication::STATUS_CONFIRMED,
        ]);
        $refusedModel = new ContractDeleteApplicationSearch([
            'status' => ContractDeleteApplication::STATUS_REFUSED,
        ]);

        $dataProvider = $waitingModel->search(\Yii::$app->request->queryParams);
        $dataConfirmedProvider = $confirmedModel->search(\Yii::$app->request->queryParams);
        $dataRefusedProvider = $refusedModel->search(\Yii::$app->request->queryParams);

        return $this->render('contract', [
            'waitingModel' => $waitingModel,
            'confirmedModel' => $confirmedModel,
            'refusedModel' => $refusedModel,
            'dataProvider' => $dataProvider,
            'dataConfirmedProvider' => $dataConfirmedProvider,
            'dataRefusedProvider' => $dataRefusedProvider,
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionCreate($id)
    {
        $model = new ContractDeleteApplication([
            'status' => ContractDeleteApplication::STATUS_WAITING,
            'contract_id' => $id
        ]);
        $model->setScenario($model::SCENARIO_CREATE);

        if ($model->load(\Yii::$app->request->post()) && ($model->contract_id = $id) && $model->save()) {
            \Yii::$app->session->setFlash('success', 'Запрос успешно отправлен.');
            return $this->redirect(['contract']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionContractList()
    {
        $searchModel = new ContractsSearch([
            'organization_id' => \Yii::$app->user->identity->organization->id,
            'modelName' => 'ContractsSearch',
        ]);
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('contract-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
