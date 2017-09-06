<?php

namespace app\controllers\admin;

use app\models\Contracts;
use Yii;
use app\models\forms\ContractRemoveForm;
use app\models\Cleanup;
use yii\web\UploadedFile;

class CleanupController extends \yii\web\Controller
{
    public function actionContract()
    {
        $model = new ContractRemoveForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
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

    public function actionCertificate()
    {
        $model = new Cleanup();

        if (Yii::$app->request->isPost) {
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            if ($model->removeChildrenFromCsv()) {
                Yii::$app->session->setFlash('success', 'Сертификаты успешно удалены.');

                return $this->refresh();
            }
        }

        return $this->render('children', [
            'model' => $model,
        ]);
    }
}
