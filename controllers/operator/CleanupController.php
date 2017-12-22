<?php

namespace app\controllers\operator;

use app\models\ContractDeleteApplication;
use app\models\forms\ContractDeleteApplicationForm;
use app\models\search\ContractDeleteApplicationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CleanupController extends Controller
{
    /**
     * @return string
     */
    public function actionContract()
    {
        $modelForm = new ContractDeleteApplicationForm();
        $waitingModel = new ContractDeleteApplicationSearch([
            'status' => ContractDeleteApplication::STATUS_WAITING,
            'withInvoiceHaveContracts' => true,
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
            'modelForm' => $modelForm,
            'waitingModel' => $waitingModel,
            'confirmedModel' => $confirmedModel,
            'refusedModel' => $refusedModel,
            'dataProvider' => $dataProvider,
            'dataConfirmedProvider' => $dataConfirmedProvider,
            'dataRefusedProvider' => $dataRefusedProvider,
        ]);
    }

    public function actionResolution()
    {
        //TODO: Запретить удалять договоры в чужих муниципалитетах
        $model = new ContractDeleteApplicationForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $application = ContractDeleteApplication::findOne(['id' => $model->appId]);
            if (!$application) {
                throw new NotFoundHttpException('Заявка не найдена');
            }

            $contract = $application->contract;
            if (!$contract) {
                throw new NotFoundHttpException('Договор не найден');
            }
            if (intval($model->status)) { // Если подтверждаем
                if ($contract->invoiceHaveContracts) {
                    \Yii::$app->session->setFlash('error',
                        'Договор нельзя удалить, он включен, как минимум в один из выставленных счетов.');
                } else {
                    if ($application->deleteContractConfirm()) {
                        \Yii::$app->session->setFlash('success', 'Договор удален.');
                    } else {
                        \Yii::$app->session->setFlash('error', 'Не удалось удалить договор.');
                    }
                }
            } else { // Если отклоняем
                if ($application->deleteContractReject()) {
                    \Yii::$app->session->setFlash('success', 'Запрос отклонен.');
                } else {
                    \Yii::$app->session->setFlash('error', 'Не удалось отклонить запрос.');
                }
            }
        }
        return $this->redirect(['contract']);
    }

}
