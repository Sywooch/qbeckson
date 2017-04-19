<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use app\models\Contracts;
use app\models\Programs;
use app\models\Certificates;
use app\models\Organization;
use app\models\Completeness;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class RecalculationController extends Controller
{
    public function actionCompleatnesscreate()
    {
        $contracts5 = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['status' => 1])
            ->column();

        foreach ($contracts5 as $contract5) {

            $model = Contracts::findOne($contract5);

            $com_pre = (new \yii\db\Query())
                ->select(['completeness', 'id'])
                ->from('completeness')
                ->where(['contract_id' => $model->id])
                ->andWhere(['month' => date('m')])
                ->andWhere(['preinvoice' => 1])
                ->one();

            $com = (new \yii\db\Query())
                ->select(['completeness', 'id'])
                ->from('completeness')
                ->where(['contract_id' => $model->id])
                ->andWhere(['month' => date('m') - 1])
                ->andWhere(['preinvoice' => 0])
                ->one();

            if (empty($com) && empty($com_pre)) {

                $completeness = new Completeness();
                $completeness->group_id = $model->group_id;
                $completeness->contract_id = $model->id;

                $start_edu_contract = explode("-", $model->start_edu_contract);

                if (date('m') == 12) {
                    $completeness->month = 12;
                    $completeness->year = date('Y');
                    // TODO тут надо создавать еще и за 11 месяц
                } else {
                    // Если месяц 1 - за предыдущий вообще не надо создавать.
                    $completeness->month = date('m') - 1;
                    $completeness->year = date('Y');
                }

                $completeness->preinvoice = 0;
                $completeness->completeness = 100;

                $month = $start_edu_contract[1];

                if ($month == date('m') - 1) {
                    $price = $model->first_m_price * $model->payer_dol;
                } else {
                    $price = $model->other_m_price * $model->payer_dol;
                }

                $completeness->sum = ($price * $completeness->completeness) / 100;

                if (date('m') != 1) {
                    $completeness->save();
                }

                $preinvoice = new Completeness();
                $preinvoice->group_id = $model->group_id;
                $preinvoice->contract_id = $model->id;
                $preinvoice->month = date('m');
                $preinvoice->year = date('Y');
                $preinvoice->preinvoice = 1;
                $preinvoice->completeness = 80;

                $start_edu_contract = explode("-", $model->start_edu_contract);
                $month = $start_edu_contract[1];

                if ($month == date('m')) {
                    $price = $model->first_m_price * $model->payer_dol;
                } else {
                    $price = $model->other_m_price * $model->payer_dol;
                }

                $preinvoice->sum = ($price * $preinvoice->completeness) / 100;
                $preinvoice->save();


            }
        }
        echo "ok";
    }

    public function actionTerminate()
    {
        $contracts = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['wait_termnate' => 1])
            ->column();

        foreach ($contracts as $contract) {

            $cont = Contracts::findOne($contract);

            $program = Programs::findOne($cont->program_id);

            if ($cont->terminator_user == 1) {
                $program->last_s_contracts_rod++;
            }
            $program->last_s_contracts++;
            $program->last_contracts--;

            //$program->last_contracts = $program->last_contracts+1;
            $org = Organization::findOne($cont->organization_id);
            $org->amount_child--;
            $org->save();

            /* $certificate = Certificates::findOne($cont->certificate_id);
           $certificate->rezerv = $certificate->rezerv - $cont->rezerv;
           $certificate->save(); */

            $program->save();

            //$cont->rezerv = 0;
            $cont->status = 4;
            $cont->wait_termnate = 0;
            if (date("m") == 1) {
                $cal_days_in_month = cal_days_in_month(CAL_GREGORIAN, 12, date('Y') - 1);
                $cont->date_termnate = (date("Y") - 1) . '-12-' . $cal_days_in_month;
            } else {
                $cal_days_in_month = cal_days_in_month(CAL_GREGORIAN, date('m') - 1, date('Y'));
                $cont->date_termnate = date("Y") . '-' . (date('m') - 1) . '-' . $cal_days_in_month;
            }
            $cont->save();


            // по этим договорам возвращать резерв на баланс + оставшиеся месяца * ежемесячный платеж
            // дата окончания - дата удаления
        }

        $contracts3 = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['like', 'stop_edu_contract', date("Y-m-")])
            ->column();


        foreach ($contracts3 as $contract) {
            $cont = $this->findModel($contract);
            $cont->wait_termnate = 1;
            $cont->save();
        }

        echo "ok";
    }

    public function actionWriteoff()
    {
        $datestart = date("Y-m") . '-01';

        $contracts2 = (new \yii\db\Query())
            ->select(['id', 'certificate_id'])
            ->from('contracts')
            ->where(['status' => 1])
            ->andWhere(['<', 'start_edu_contract', $datestart])
            ->all();

        foreach ($contracts2 as $contract2) {
            $model = Contracts::findOne($contract2['id']);

            $certificates = (new \yii\db\Query())
                ->select(['id'])
                ->from('certificates')
                ->where(['>', 'rezerv', 0])
                ->andWhere(['id' => $contract2['certificate_id']])
                ->column();


            foreach ($certificates as $certificate) {
                $cert = Certificates::findOne($certificate);
                //$cert->balance = $cert->balance - $cert->rezerv;
                $model->rezerv -= $model->other_m_price * $model->payer_dol;
                $model->paid += $model->other_m_price * $model->payer_dol;
                $cert->rezerv -= $model->other_m_price * $model->payer_dol;

                $model->save();
                $cert->save();
            }
        }

        echo "ok";
    }

    public function actionCompleatnessrefound()
    {
        $contracts4 = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['status' => [1, 4]])
            ->column();

        if (date('m') == 1) {
            $twomonth = 11;
        }
        if (date('m') == 2) {
            $twomonth = 12;
        }
        if (date('m') > 2) {
            $twomonth = date('m') - 2;
        }

        foreach ($contracts4 as $contract4) {
            $contract = Contracts::findOne($contract4);

            $completeness = (new \yii\db\Query())
                ->select(['completeness'])
                ->from('completeness')
                ->where(['month' => $twomonth])
                ->andWhere(['preinvoice' => 0])
                ->andWhere(['contract_id' => $contract->id])
                ->one();

            if ($completeness['completeness'] < 100 && isset($completeness['completeness'])) {

                $certificate = Certificates::findOne($contract->certificate_id);

                $start_edu_contract = explode('-', $contract->start_edu_contract);

                if ($start_edu_contract[1] == $twomonth) {
                    $certificate->balance = $certificate->balance + (($contract->first_m_price * $contract->payer_dol) / 100) * (100 - $completeness['completeness']);
                } else {
                    $certificate->balance = $certificate->balance + (($contract->other_m_price * $contract->payer_dol) / 100) * (100 - $completeness['completeness']);
                }

                $certificate->save();
            }
        }

        echo "ok";
    }
}
