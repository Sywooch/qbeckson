<?php

namespace app\controllers\organization;

use app\helpers\ArrayHelper;
use app\models\ContractDeleteApplication;
use app\models\Contracts;
use app\models\search\ContractDeleteApplicationSearch;
use app\models\search\ContractsSearch;
use app\models\UserIdentity;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CleanupController extends Controller
{
    /**
     * @return string
     */
    public function actionContract()
    {
        /** @var UserIdentity $identity */
        $identity = \Yii::$app->user->identity;
        $waitingModel = new ContractDeleteApplicationSearch([
            'status' => ContractDeleteApplication::STATUS_WAITING,
            'organizationId' => ArrayHelper::getValue($identity, ['organization', 'id']),
        ]);
        $confirmedModel = new ContractDeleteApplicationSearch([
            'status' => ContractDeleteApplication::STATUS_CONFIRMED,
            'organizationId' => ArrayHelper::getValue($identity, ['organization', 'id']),
        ]);
        $refusedModel = new ContractDeleteApplicationSearch([
            'status' => ContractDeleteApplication::STATUS_REFUSED,
            'organizationId' => ArrayHelper::getValue($identity, ['organization', 'id']),
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
     * @throws NotFoundHttpException
     */
    public function actionCreate($id)
    {
        /** @var UserIdentity $identity */
        $identity = \Yii::$app->user->identity;
        $organizationId = ArrayHelper::getValue($identity, ['organization', 'id']);
        // Проверяем есть ли такой контракт у этой организации
        $contract = Contracts::findOne([
            'id' => $id,
            'organization_id' => $organizationId
        ]);
        if (!$contract) {
            throw new NotFoundHttpException('Договор не найден');
        }

        //Если запрос на удаление этого договора уже создан
        if (ContractDeleteApplication::find()->where(['contract_id' => $id,])->exists()) {
            return $this->render('exists');
        }

        $model = new ContractDeleteApplication([
            'status' => ContractDeleteApplication::STATUS_WAITING,
            'contract_id' => $id,
            'organization_id' => $organizationId,
        ]);
        $model->setScenario($model::SCENARIO_CREATE);

        if ($model->load(\Yii::$app->request->post())) {
            $model->status = ContractDeleteApplication::STATUS_WAITING;
            $model->contract_id = $id;
            $model->organization_id = $organizationId;
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', 'Запрос успешно отправлен.');
                return $this->redirect(['contract']);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionContractList()
    {
        /** @var UserIdentity $identity */
        $identity = \Yii::$app->user->identity;
        $searchModel = new ContractsSearch([
            'organization_id' => ArrayHelper::getValue($identity, ['organization', 'id']),
            'modelName' => 'ContractsSearch',
        ]);
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('contract-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
