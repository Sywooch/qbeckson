<?php

namespace app\services;

use app\models\Certificates;
use Yii;

/**
 * Class PayerService
 * @package app\services
 */
class PayerService
{
    /**
     * Можно как-то избавиться от $type = 'current', сделать какой-то $suffixType = '' или '_f' или '_p'
     * И относительно него уже всё делать, без условий.
     *
     * @param integer $groupId
     * @param float $newNominal
     * @param string $type
     * @return bool|string
     */
    public function updateCertificateNominal($groupId, $newNominal, $type = 'current')
    {
        if ($type === 'current') {
            $exist = Certificates::find()
                ->andWhere('balance < nominal - ' . $newNominal)
                ->andWhere(['cert_group' => $groupId])
                ->exists();
        }
        if ($type === 'future') {
            $exist = Certificates::find()
                ->andWhere('balance_f < nominal_f - ' . $newNominal)
                ->andWhere(['cert_group' => $groupId])
                ->exists();
        }
        if ($exist) {
            return 'Нельзя обновить номинал';
        }

        $db = Yii::$app->db;
        //Баланс = новый номинал - старый номинал + старый баланс
        if ($type === 'current') {
            /*$command= $db->createCommand()->update(
                'certificates',
                [
                    'balance' => 'balance - nominal + ' . $newNominal,
                    'nominal' => $newNominal
                ],
                "cert_group = {$groupId}"
            );*/
            $command = $db->createCommand(
                'UPDATE certificates SET 
                    balance = (balance - nominal + ' . $newNominal . '),
                    nominal = ' . $newNominal .' WHERE cert_group = ' . $groupId
            );
        }
        if ($type === 'future') {
            /*$command = $db->createCommand()->update(
                'certificates',
                [
                    'balance_f' => 'balance_f - nominal_f + ' . $newNominal,
                    'nominal_f' => $newNominal
                ],
                "cert_group = {$groupId}"
            );*/
            $command = $db->createCommand(
                'UPDATE certificates SET 
                    balance_f = (balance_f - nominal_f + ' .$newNominal . '),
                    nominal_f = ' . $newNominal .' WHERE cert_group = ' . $groupId
            );
        }
        $command->execute();

        return true;
    }
}
