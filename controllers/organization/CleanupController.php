<?php

namespace app\controllers\organization;

use app\models\Contracts;
use Yii;
use app\models\forms\ContractRemoveForm;
use yii\web\Controller;

class CleanupController extends Controller
{
    public function actionContract()
    {
        $model = new ContractRemoveForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /** @var Contracts $contracts */
            if ($contracts = Contracts::findByInterval($model->contractIdStart, $model->contractIdFinish, $model->organizationId)) {
                foreach ($contracts as $contract) {
                    if ($contract->refoundMoney()) {
                        $contract->delete();
                    }
                }

                Yii::$app->session->setFlash('success', 'Контракты успешно удалены.');
            } else {
                Yii::$app->session->setFlash('success', 'Контрактов не найдено.');
            }

            $this->refresh();
        }

        return $this->render('contract-remove', [
            'model' => $model,
        ]);
    }

}
