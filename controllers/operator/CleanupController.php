<?php

namespace app\controllers\operator;

use app\models\ContractDeleteApplication;
use app\models\Contracts;
use app\models\forms\ContractDeleteApplicationForm;
use app\models\search\ContractDeleteApplicationSearch;
use yii\db\Expression;
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
        //TODO: Разобраться с внешними ключами (пример договор N 35)
        $model = new ContractDeleteApplicationForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $application = ContractDeleteApplication::findOne(['id' => $model->appId]);
            if (!$application) {
                throw new NotFoundHttpException('Заявка не найдена');
            }
            $application->confirmed_at = new Expression('NOW()');

            $contract = Contracts::findOne(['id' => $model->contractId]);
            if (!$contract || $application->contract_id !== $contract->id) {
                throw new NotFoundHttpException('Договор не найден');
            }
            if (intval($model->status)) {
                $application->status = ContractDeleteApplication::STATUS_CONFIRMED;
                $transaction = \Yii::$app->db->beginTransaction();
                if ($contract->refoundMoney() && $contract->delete() !== false && $application->save(false)) {
                    $transaction->commit();
                    \Yii::$app->session->setFlash('success', 'Договор удален.');
                } else {
                    $transaction->rollBack();
                    \Yii::$app->session->setFlash('error', 'Не удалось удалить договор.');
                }
            } else {
                $application->status = ContractDeleteApplication::STATUS_REFUSED;
                if ($application->save(false)) {
                    \Yii::$app->session->setFlash('success', 'Запрос отклонен.');
                } else {
                    \Yii::$app->session->setFlash('error', 'Не удалось отклонить запрос.');
                }
            }
        }
        return $this->redirect(['contract']);
    }

}
