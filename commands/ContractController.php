<?php
namespace app\commands;

use app\models\Completeness;
use app\models\Contracts;
use app\models\Cooperate;
use app\models\Certificates;
use app\models\Operators;
use app\models\Payers;
use app\models\UserIdentity;
use yii;
use yii\console\Controller;

/*
php yii contract/close
php yii contract/write-off
php yii contract/completeness-refound
php yii contract/completeness-create
*/

class ContractController extends Controller
{
    public function actionShiftPeriod()
    {
        $operators = Operators::find()->all();

        foreach ($operators as $operator) {
            $settings = $operator->settings;
            if (empty($settings) || $settings->current_program_date_to > date('Y-m-d')) {
                continue;
            }
            // Сдвигаем периоды оператора
            $settings->current_program_date_to = $settings->future_program_date_to;
            $settings->current_program_date_from = $settings->future_program_date_from;
            $baseDate = $settings->future_program_date_to;
            $settings->future_program_date_from = date("Y-m-d", strtotime("+1 day", strtotime($baseDate)));
            $settings->future_program_date_to = date("Y-m-d", strtotime("+1 year", strtotime($baseDate)));
            if ($settings->save()) {
                $arrayPayersIds = Payers::find()
                    ->select('id')
                    ->where(['operator_id' => $operator->id])
                    ->indexBy('id')
                    ->column();
                $payersIds = join(',', $arrayPayersIds);
                // Плательщики
                $command = Yii::$app->db->createCommand("UPDATE payers AS p SET certificate_can_use_future_balance = 0, certificate_can_use_current_balance = 1 WHERE id IN ($payersIds)");
                print_r($command->rawSql);
                echo PHP_EOL . ' --> ' . $command->execute() . PHP_EOL;
                // Cert groups
                date_default_timezone_set('Europe/Moscow');
                $datetimeTo = date_create($settings->current_program_date_to);
                $datetimeFrom = date_create($settings->current_program_date_from);
                $difference = date_diff($datetimeFrom, $datetimeTo);
                $coefficient = 365 / ($difference->format('%a') + 1);
                $command = Yii::$app->db->createCommand("UPDATE cert_group SET nominal = nominal_f, nominal_f = ROUND(nominal_f * :coefficient) WHERE payer_id IN ($payersIds)", [
                    ':coefficient' => $coefficient,
                ]);
                print_r($command->rawSql);
                echo PHP_EOL . ' --> ' . $command->execute() . PHP_EOL;
                // Сертификаты
                $command = Yii::$app->db->createCommand("UPDATE `certificates` as c INNER JOIN `cert_group` as cg ON c.cert_group = cg.id SET c.nominal_p = c.nominal, c.balance_p = c.balance, c.rezerv_p = c.rezerv, c.nominal = c.nominal_f, c.balance = c.balance_f, c.rezerv = c.rezerv_f, c.nominal_f = cg.nominal_f, c.balance_f = cg.nominal_f, rezerv_f = 0 WHERE c.payer_id IN ($payersIds)");
                print_r($command->rawSql);
                echo PHP_EOL . ' --> ' . $command->execute() . PHP_EOL;
                // Договоры
                $command = Yii::$app->db->createCommand("UPDATE `contracts` SET period = " . Contracts::PAST_REALIZATION_PERIOD . " WHERE payer_id IN ($payersIds) AND period = " . Contracts::CURRENT_REALIZATION_PERIOD);
                print_r($command->rawSql);
                echo PHP_EOL;
                $command->execute();
                $command = Yii::$app->db->createCommand("UPDATE `contracts` SET period = " . Contracts::CURRENT_REALIZATION_PERIOD . " WHERE payer_id IN ($payersIds) AND period = " . Contracts::FUTURE_REALIZATION_PERIOD);
                print_r($command->rawSql);
                echo PHP_EOL;
                $command->execute();
                // Кооперейты
                $command = Yii::$app->db->createCommand("UPDATE `cooperate` SET period = " . Cooperate::PERIOD_ARCHIVE . " WHERE payer_id IN ($payersIds) AND period = " . Cooperate::PERIOD_CURRENT);
                print_r($command->rawSql);
                echo PHP_EOL;
                $command->execute();
                $command = Yii::$app->db->createCommand("UPDATE `cooperate` SET period = " . Cooperate::PERIOD_CURRENT . " WHERE payer_id IN ($payersIds) AND period = " . Cooperate::PERIOD_FUTURE);
                print_r($command->rawSql);
                echo PHP_EOL;
                $command->execute();
            } else {
                print_r($settings->errors);

                return Controller::EXIT_CODE_ERROR;
            }
        }
        echo 'Done.';

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

        echo 'Done.';

        return Controller::EXIT_CODE_NORMAL;
    }

    /**
     * расторгнуть (contracts.status = 4) те контракты, у которых stop_edu_contract меньше текущего дня,
     * день расторжения договора ставится stop_edu_contract
     */
    public function actionCloseOnExpired()
    {
        Yii::$app->db->createCommand('
          update contracts as c CROSS JOIN programs as p ON c.program_id = p.id CROSS JOIN organization as o ON c.organization_id = o.id
          set c.status = 4, c.wait_termnate = 0, c.date_termnate = c.stop_edu_contract, p.last_s_contracts_rod = IF(c.terminator_user = 1, p.last_s_contracts_rod + 1, p.last_s_contracts_rod), p.last_contracts = p.last_contracts - 1, p.last_s_contracts = p.last_s_contracts + 1, o.amount_child = o.amount_child - 1
          WHERE c.stop_edu_contract < :phpDate and c.status = 1 AND c.wait_termnate = 1',  [':phpDate' => date('Y-m-d H:i:s')])->execute();

        Yii::$app->db->createCommand('
          delete from contracts
          WHERE contracts.status is NULL and :phpDate > DATE_ADD(contracts.created_at, INTERVAL 2 DAY)
        ', [':phpDate' => date('Y-m-d H:i:s')])->execute();

        echo 'Done.';

        return Controller::EXIT_CODE_NORMAL;
    }

    // Списание средств за месяц
    public function actionWriteOff()
    {
        // == Вынимаем действующие контракты, дата начала обучения которых меньше первого числа текущего месяца
        // Для контракта уменьшаем rezerv, увеличиваем paid
        // Для связанного сертификата уменьшаем rezerv
        $datestart = date('Y-m-d', strtotime('first day of this month'));

        $contracts = Contracts::find()
            ->where(['status' => Contracts::STATUS_ACTIVE])
            ->andWhere(['<', 'start_edu_contract', $datestart])
            ->all();

        foreach ($contracts as $contract) {
            $certificate = Certificates::find()
                ->where(['>', 'rezerv', 0])
                ->andWhere(['id' => $contract->certificate_id])
                ->one();

            if (!$certificate) {
                continue;
            }

            echo $contract->id . PHP_EOL;

            $contract->rezerv = round($contract->rezerv - $contract->payer_other_month_payment, 2);
            $contract->paid = round($contract->paid + $contract->payer_other_month_payment, 2);
            $certificate->rezerv = round($certificate->rezerv - $contract->payer_other_month_payment, 2);

            if (!$contract->save(false, ['rezerv', 'paid']) || !$certificate->save(false, ['rezerv'])) {
                print_r($contract->errors);
                print_r($certificate->errors);

                die('Error while saving.');
            }
        }
        echo 'Done.';

        return Controller::EXIT_CODE_NORMAL;
    }

    public function actionCompletenessCreate()
    {
        // TODO: временный костыль, переделать логику
        ini_set('memory_limit', '-1');

        $previousMonth = strtotime('first day of previous month');
        $currentMonth = strtotime('first day of this month');
        $lastDayOfThisMonth = strtotime('last day of this month');

        $query = Contracts::find()
            ->where(['<=', 'start_edu_contract', date('Y-m-d', $lastDayOfThisMonth)])
            ->andWhere(['or', ['status' => Contracts::STATUS_ACTIVE], ['and', ['status' => Contracts::STATUS_CLOSED], ['>=', 'date_termnate', date('Y-m-d', $previousMonth)]]]);

        $contracts = $query->all();

        $i = 1;
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
            if (!$completenessExists && $contract->start_edu_contract < date('Y-m-d', $currentMonth)) {
                if (!$this->createCompleteness($contract, $previousMonth, $this->monthlyPrice($contract, $previousMonth))) {
                    echo('Ошибка создание счета.');

                    continue;
                }
                echo PHP_EOL . $i++ . '. Создал для ' . $contract->id . ' счет за ' . date('d.m.Y', $previousMonth) . PHP_EOL;
            }
            // Если текущий месяц == декабрь, то тоже создаем
            if (date('m') == 12) {
                $completenessDecemberExists = Completeness::find()
                    ->where([
                        'contract_id' => $contract->id,
                        'preinvoice' => 0,
                        'month' => date('m', time()),
                        'year' => date('Y', time()),
                    ])
                    ->count();
                if (!$completenessDecemberExists) {
                    $this->createCompleteness($contract, time(), $this->monthlyPrice($contract, time()));
                }
            }
            // Создаем преинвойс
            if (!$preinvoiceExists && $contract->status == Contracts::STATUS_ACTIVE && $contract->start_edu_contract <= date('Y-m-d', $lastDayOfThisMonth)) {
                echo PHP_EOL . $i++ . '. Создал для ' . $contract->id . ' аванс за ' . date('d.m.Y') . PHP_EOL;
                if (!$this->createPreinvoice($contract, $this->monthlyPrice($contract, time()))) {
                    die('Ошибка создание аванса.');
                }
            }
        }

        return Controller::EXIT_CODE_NORMAL;
    }

    // Одноразовый метод, не для крона
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

    /**
     * Отклонение всех подтвержденных договоров и заявок,
     * дата которых меньше чем 1-е число прошлого месяца
     * с мотивировкой о невозможности заключения договоров задним числом
     */
    public function actionContractsRefuse()
    {
        $previousMonth = date('m', strtotime('-2 month'));

        $contracts = Contracts::find()
            ->where('MONTH(start_edu_contract) = ' . $previousMonth)
            ->andWhere('status = ' . Contracts::STATUS_REQUESTED .' or status = ' . Contracts::STATUS_ACCEPTED)
            ->all();

        /** @var Contracts $contract */
        foreach ($contracts as $contract) {
            $contract->setRefused('Оферта отозвана в связи с превышением сроков акцепта (невозможно заключить договор задним числом)', UserIdentity::ROLE_OPERATOR_ID, null);
        }
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

        if (!$completeness->save()) {
            print_r($completeness->errors);

            return false;
        }

        return true;
    }

    private function createPreinvoice($contract, $price)
    {
        $preinvoice = new Completeness([
            'group_id' => $contract->group_id,
            'contract_id' => $contract->id,
            'preinvoice' => 1,
            'completeness' => 80,
            'sum' => round(($price * 80) / 100, 2),
            'month' => date('m'),
            'year' => date('Y'),
        ]);

        return $preinvoice->save();
    }

    /**
     * @deprecated
     *
     * @param $contract
     * @param $date
     *
     * @return mixed
     */
    private function monthlyPrice($contract, $date)
    {
        return $contract->getMonthlyPrice($date);
    }

    public function actionFindWithoutReserve()
    {
        $contracts = Contracts::find()
            ->where(['>', 'wait_termnate', 0])
            ->all();


        foreach ($contracts as $contract) {
            $certificateContracts = Contracts::find()
                ->where(['certificate_id' => $contract->certificate->id])
                ->all();
            $balance = 0;
            foreach ($certificateContracts as $certificateContract) {
                $balance += $certificateContract->rezerv + $certificateContract->paid;
            }

            $balance = $contract->certificate->nominal - $balance;

            if (round($balance, 2) != round($contract->certificate->balance, 2)) {
                echo "certificate_id: {$contract->certificate->id} ===> {$contract->certificate->balance} != {$balance}" . PHP_EOL;
            }
        }

        return Controller::EXIT_CODE_NORMAL;
    }

    public function actionCronTest()
    {
        $contracts = Contracts::find()
            ->limit(10)
            ->all();

        Yii::trace('Тестовое количество контрактов ' . count($contracts));
        Yii::trace('Тестирование завершено.');

        return Controller::EXIT_CODE_NORMAL;
    }
}
