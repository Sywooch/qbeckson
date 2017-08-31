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

    // Для всех сертификатов, у которых резерв = 0, надо баланс текущий и будущий сделать соответствующим номиналу.
    public function actionUpdateBalance()
    {
        $command = Yii::$app->db->createCommand("UPDATE certificates SET balance = nominal WHERE rezerv = 0");
        $command->execute();

        $command = Yii::$app->db->createCommand("UPDATE certificates SET balance_f = nominal_f WHERE rezerv_f = 0");
        $command->execute();
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

    public function actionUpdatePossibleCertGroupPf($pf = false)
    {
        $query = Certificates::find()
            ->joinWith('certGroup')
            ->andWhere(['possible_cert_group' => 0]);

        if (!$pf) {
            $query->andWhere("`cert_group`.is_special < 1 OR `cert_group`.is_special IS NULL");
        } else {
            $query->andWhere("`cert_group`.is_special > 0");
        }

        $models = $query->all();

        foreach ($models as $model) {
            $model->possible_cert_group = $model->cert_group;
            echo $model->id . PHP_EOL;
            if (!$model->save(false, ['possible_cert_group'])) {
                return self::EXIT_CODE_ERROR;
            }
        }

        return self::EXIT_CODE_NORMAL;
    }

    public function actionUpdatePossibleCertGroup()
    {
        $query = Certificates::find()
            ->joinWith('possibleCertGroup')
            ->joinWith('payer.firstCertGroup')
            ->where('`cert_group`.is_special > 0')
            ->groupBy('`certificates`.id');

        $models = $query->all();

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
