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
     * @param integer $groupId
     * @param float $newNominal
     * @param string $suffix
     * @return bool|string
     */
    public function updateCertificateNominal($groupId, $newNominal, $suffix = '')
    {
        $exist = Certificates::find()
            ->andWhere('balance' . $suffix . ' < nominal' . $suffix . ' - ' . $newNominal)
            ->andWhere(['cert_group' => $groupId])
            ->exists();
        if ($exist) {
            return 'Предлагаемый номинал не может быть установлен в силу того, что часть сертификатов уже заключили договоры на большую сумму.';
        }

        Yii::$app->db->createCommand('UPDATE certificates SET
            balance' . $suffix . ' = (balance' . $suffix . ' - nominal' . $suffix . ' + ' . $newNominal . '),
            nominal' . $suffix . ' = ' . $newNominal .' WHERE cert_group = ' . $groupId
        )->execute();

        return true;
    }
}
