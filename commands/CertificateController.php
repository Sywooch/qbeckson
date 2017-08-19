<?php

namespace app\commands;

use app\models\Certificates;
use yii;
use yii\console\Controller;

class CertificateController extends Controller
{
    public function actionUpdatePossibleCertGroup()
    {
        $models = Certificates::find()
            ->joinWith('possibleCertGroup')
            ->joinWith('payer.firstCertGroup')
            ->where('`cert_group`.is_special > 0')
            ->all();

        foreach ($models as $model) {
            $model->possible_cert_group = $model->payer->firstCertGroup->id;
            echo $model->id . PHP_EOL;
            if (!$model->save(false, ['possible_cert_group'])) {
                return self::EXIT_CODE_ERROR;
            }
        }

        return self::EXIT_CODE_NORMAL;
    }

    public function actionActivateAll()
    {
        $query = Certificates::find()
            ->joinWith('payer.accountingCertGroup')
            ->andWhere(['<', 'actual', 1]);

        $models = $query->all();

        foreach ($models as $model) {
            $model->cert_group = $model->payer->accountingCertGroup->id;
            $model->actual = 1;
            echo $model->id . PHP_EOL;
            if (!$model->save(false, ['actual', 'cert_group'])) {
                return self::EXIT_CODE_ERROR;
            }
        }

        return self::EXIT_CODE_NORMAL;
    }
}
