<?php

namespace app\commands;

use app\models\Certificates;
use yii;
use yii\console\Controller;
use app\services\PayerService;

class CertificateController extends Controller
{
    private $payerService;

    public function init()
    {
        $this->payerService = new PayerService;
        parent::init();
    }

    public function actionUpdateNominals()
    {
        $groups = Certificates::find()
            ->joinWith('certGroup')
            ->where("`cert_group`.is_special < 1 OR `cert_group`.is_special IS NULL")
            ->groupBy('cert_group')
            ->all();

        foreach ($groups as $group) {
            $this->payerService->updateCertificateNominal($group->certGroup->id, $group->certGroup->nominal, '', false);
            $this->payerService->updateCertificateNominal($group->certGroup->id, $group->certGroup->nominal_f, '_f', false);
            echo '--> ' . $group->certGroup->id . PHP_EOL;
        }
    }

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
