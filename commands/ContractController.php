<?php
namespace app\commands;

use yii;
use yii\console\Controller;
use app\models\Contracts;
use app\models\Programs;
use app\models\Certificates;
use app\models\Organization;
use app\models\Completeness;

/*
php yii contract/close
php yii contract/write-off
php yii contract/completeness-refound
php yii contract/completeness-create
*/

class ContractController extends Controller
{
    // Подготовка к тесту Close
    public function actionPrepareCloseTest()
    {
        // Для контрактов обнуляем параметры
        Contracts::updateAll(['wait_termnate' => 0, 'date_termnate' => 'NULL', 'stop_edu_contract' => 'NULL', 'status' => 1]);
        // Для контрактов случайно ставим wait_termnate = 1
        Contracts::updateAll(['wait_termnate' => 1], 'RAND() <= 0.02');
        // Для программ обнуляем last_s_contracts_rod, last_s_contracts, last_contracts
        Programs::updateAll(['last_s_contracts_rod' => 0, 'last_s_contracts' => 0, 'last_contracts' => 0]);
        // Для организаций ставим amount_child = 10
        Organization::updateAll(['amount_child' => 10]);
        // Для нескольких контрактов случайно ставим stop_edu_contract текущим месяцем
        Contracts::updateAll(['stop_edu_contract' => date('Y-m-d')], 'RAND() <= 0.02');
        // Для нескольких контрактов случайно ставим stop_edu_contract будущим месяцем
        Contracts::updateAll(['stop_edu_contract' => date('Y-m-d', time() + 40 * 24 * 60 * 60)], 'RAND() <= 0.02');

        return Controller::EXIT_CODE_NORMAL;
    }

    public function actionClose()
    {
        // == Контракты, которые ждут закрытия:
        // Изменяем счетчики для программы контракта
        // Изменяем счетчик для организатора контракта
        // Меняем статус контракта на "закрытый" (4)
        // Дату закрытия ставим последним днем предыдущего месяца
        $command = Yii::$app->db->createCommand("UPDATE contracts as c CROSS JOIN programs as p ON c.program_id = p.id CROSS JOIN organization as o ON c.organization_id = o.id SET c.status = 4, c.wait_termnate = 0, c.date_termnate = :date_terminate, p.last_s_contracts_rod = IF(c.terminator_user = 1, p.last_s_contracts_rod + 1, p.last_s_contracts_rod), p.last_contracts = p.last_contracts - 1, p.last_s_contracts = p.last_s_contracts + 1, o.amount_child = o.amount_child - 1 WHERE c.wait_termnate > 0", [
            ':date_terminate' => date('Y-m-d', strtotime('last day of previous month')),
        ]);
        $command->execute();

        // == Ищем контракты, которые поставлены на закрытие в текущем месяце
        // Меняем им wait_termnate на 1
        $command = Yii::$app->db->createCommand("UPDATE contracts as c SET c.wait_termnate = 1 WHERE c.status = 1 AND MONTH(c.stop_edu_contract) = :month AND YEAR(c.stop_edu_contract) = :year", [
            ':year' => date('Y'),
            ':month' => date('m'),
        ]);
        $command->execute();

        return Controller::EXIT_CODE_NORMAL;
    }

    // Подготовка к тесту Write Off
    public function actionPrepareWriteOffTest()
    {
        Yii::$app->db->createCommand()->delete('contracts')->execute();
        Certificates::updateAll(['balance' => 5000, 'rezerv' => 0]);

        return Controller::EXIT_CODE_NORMAL;
    }

    // Списание средств за месяц
    public function actionWriteOff()
    {
        // == Вынимаем действующие контракты, дата начала обучения которых меньше первого числа текущего месяца
        // Для контракта уменьшаем rezerv, увеличиваем paid
        // Для связанного сертификата уменьшаем rezerv
        $command = Yii::$app->db->createCommand("UPDATE contracts as c CROSS JOIN certificates as crt ON c.certificate_id = crt.id SET crt.rezerv = c.rezerv - c.other_m_price * c.payer_dol, c.rezerv = c.rezerv - c.other_m_price * c.payer_dol, c.paid = c.paid + c.other_m_price * c.payer_dol WHERE c.status = 1 AND c.start_edu_contract < :contract_start", [
            ':contract_start' => date('Y-m-d', strtotime('first day of this month')),
        ]);
        $command->execute();

        return Controller::EXIT_CODE_NORMAL;
    }

    public function actionCompletenessRefound()
    {
        // TODO: временный костыль, переделать логику
        ini_set('memory_limit', '-1');

        $contracts = Contracts::find()
            ->where(['status' => [Contracts::STATUS_ACTIVE, Contracts::STATUS_CLOSED]])
            ->all();

        $dateTwoMonthsAgo = strtotime('first day of 2 months ago');
        foreach ($contracts as $contract) {
            $completeness = Completeness::find()
                ->where([
                    'month' => date('m', $dateTwoMonthsAgo),
                    'year' => date('Y', $dateTwoMonthsAgo),
                    'preinvoice' => 0,
                    'contract_id' => $contract->id,
                ])
                ->one();

            if (!empty($completeness) && $completeness['completeness'] < 100) {
                $certificate = $contract->certificate;
                $monthlyPrice = $this->monthlyPrice($contract, $dateTwoMonthsAgo);
                $certificate->updateCounters(['balance' => (($monthlyPrice * $contract->payer_dol) / 100) * (100 - $completeness['completeness'])]);
                // TODO: Уменьшить и paid по договору, резерв не возвращая
            }
        }

        return Controller::EXIT_CODE_NORMAL;
    }

    public function actionCompletenessCreate()
    {
        // TODO: временный костыль, переделать логику
        ini_set('memory_limit', '-1');

        $previousMonth = strtotime('first day of previous month');
        $currentMonth = strtotime('first day of this month');

        $contracts = Contracts::find()
            ->where(['<=', 'start_edu_contract', date('Y-m-d', $currentMonth)])
            ->andWhere(['or', ['status' => Contracts::STATUS_ACTIVE], ['and', ['status' => Contracts::STATUS_CLOSED], ['>=', 'date_termnate', date('Y-m-d', $previousMonth)]]])
            ->all();
        // создает счета, которые только-только закрылись

        foreach ($contracts as $contract) {
            $completenessExists = Completeness::find()
                ->where([
                    'contract_id' => $contract->id,
                    'preinvoice' => 0,
                    'month' => date('m', $previousMonth),
                    'year' => date('Y', $previousMonth),
                ])
                ->count();
            $preinvoiceExists = Completeness::find()
                ->where([
                    'contract_id' => $contract->id,
                    'preinvoice' => 1,
                    'month' => date('m'),
                    'year' => date('Y'),
                ])
                ->count();
            // Создаем за предыдущий месяц
            // Если месяц январь - создаваться не будет
            if (!$completenessExists) {
                $this->createCompleteness($contract, $previousMonth, $this->monthlyPrice($contract, $previousMonth));
            }
            // Если текущий месяц == декабрь, то тоже создаем
            if (date('m') == 12) {
                $this->createCompleteness($contract, time(), $this->monthlyPrice($contract, time()));
            }
            // Создаем преинвойс
            if (!$preinvoiceExists && $contract->status == Contracts::STATUS_ACTIVE) {
                $this->createPreinvoice($contract, $this->monthlyPrice($contract, time()));
            }
        }

        return Controller::EXIT_CODE_NORMAL;
    }

    public function actionReserveRefound()
    {
        // Договора расторгаем
        $command = Yii::$app->db->createCommand("UPDATE contracts as c SET c.status = 4, c.wait_termnate = 0, c.date_termnate = :date_terminate WHERE c.status = 1 AND c.stop_edu_contract < :date_terminate", [
            ':date_terminate' => date('Y-m-d', strtotime('last day of previous month')),
        ]);
        $command->execute();

        $command = Yii::$app->db->createCommand("UPDATE contracts as c CROSS JOIN certificates as crt ON c.certificate_id = crt.id SET crt.rezerv = crt.rezerv + ABS(c.rezerv), c.paid = c.paid - ABS(c.rezerv), c.rezerv = 0 WHERE c.rezerv < 0");
        $command->execute();

        $command = Yii::$app->db->createCommand("UPDATE contracts as c CROSS JOIN certificates as crt ON c.certificate_id = crt.id SET crt.rezerv = 0 WHERE c.status = 4");
        $command->execute();
    }

    private function createCompleteness($contract, $date, $price)
    {
        $completeness = new Completeness([
            'group_id' => $contract->group_id,
            'contract_id' => $contract->id,
            'preinvoice' => 0,
            'completeness' => 100,
            'sum' => $price,
            'month' => date('m', $date),
            'year' => date('Y', $date),
        ]);

        return $completeness->save();
    }

    private function createPreinvoice($contract, $price)
    {
        $preinvoice = new Completeness([
            'group_id' => $contract->group_id,
            'contract_id' => $contract->id,
            'preinvoice' => 1,
            'completeness' => 80,
            'sum' => ($price * 80) / 100,
            'month' => date('m'),
            'year' => date('Y'),
        ]);

        return $preinvoice->save();
    }

    private function monthlyPrice($contract, $date)
    {
        $monthlyPrice = $contract->other_m_price;
        $contractStartDate = strtotime($contract->start_edu_contract);
        if (date('Y-m', $contractStartDate) == date('Y-m', $date)) {
            $monthlyPrice = $contract->first_m_price;
        }

        return $monthlyPrice;
    }
}
