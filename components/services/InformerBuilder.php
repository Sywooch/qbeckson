<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 22.09.2017
 * Time: 12:28
 */

namespace app\components\services;


use app\models\Contracts;
use app\models\Informs;
use app\models\UserIdentity;
use Yii;

/**
 * Class InformerBuilder
 * @package app\components\services
 */
class InformerBuilder
{

    public static function CreateFoContractTerminate(Contracts $contract)
    {
        if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
            return self::CreateFoContractTerminateByCertificate($contract);
        } elseif (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            return self::CreateFoContractTerminateByOrganization($contract);
        } else {
            return null;
        }
    }

    /**
     * @param Contracts $contract
     *
     * @return Informs|null
     */
    public static function CreateFoContractTerminateByCertificate(Contracts $contract)
    {
        $informs = self::build($contract, '', UserIdentity::ROLE_CERTIFICATE_ID, $contract->certificate_id);
        if (!$informs->load(Yii::$app->request->post())) {
            return null;
        }
        $informs->text = 'Договор расторжен клиентом. Причина: ' . $informs->dop;
        if ($informs->save()) {

            return $informs;
        }

        return null;
    }

    /**
     * @param Contracts $contract
     *
     * @return Informs|null
     */
    public static function CreateFoContractTerminateByOrganization(Contracts $contract)
    {
        $informs = self::build($contract, '', UserIdentity::ROLE_CERTIFICATE_ID, $contract->certificate_id);
        if (!$informs->load(Yii::$app->request->post())) {
            Yii::trace($informs->getErrors());
            return null;
        }
        $informs->text = 'Договор расторжен поставщиком услуг. Причина: ' . $informs->dop;
        if ($informs->save()) {

            return $informs;
        }

        return null;
    }

    public static function CreateFoContractRefuse(Contracts $contract)
    {
        if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
            return self::CreateFoContractRefuseByCertificate($contract);
        } elseif (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            return self::CreateFoContractRefuseByOrganization($contract);
        } elseif (Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)) {
            return self::CreateFoContractRefuseByOperator($contract);
        } else {
            return null;
        }
    }

    /**
     * @param Contracts $contract
     *
     * @return Informs|null
     */
    public static function CreateFoContractRefuseByCertificate(Contracts $contract)
    {
        $informs = self::build($contract, '', UserIdentity::ROLE_CERTIFICATE_ID, $contract->certificate_id);
        if (!$informs->load(Yii::$app->request->post())) {
            return null;
        }
        $informs->text = 'Заявка отменена клиентом. Причина: ' . $informs->dop;
        if ($informs->save()) {
            return $informs;
        }

        return null;
    }

    public static function build(Contracts $contract, $message, $from, $prof): Informs
    {
        $informs = new Informs([
            'program_id'  => $contract->program_id,
            'contract_id' => $contract->id,
            'prof_id'     => $prof,
            'text'        => $message,
            'from'        => $from,
            'date'        => date("Y-m-d"),
            'read'        => 0,
        ]);

        return $informs;
    }

    /**
     * @param Contracts $contract
     *
     * @return Informs|null
     */
    public static function CreateFoContractRefuseByOperator(Contracts $contract)
    {
        $operatpr = Yii::$app->operator->getIdentity();
        $informs = self::build($contract, '', UserIdentity::ROLE_OPERATOR_ID, $operatpr->id);
        if (!$informs->load(Yii::$app->request->post())) {
            return null;
        }
        $informs->text = 'Заявка отменена оператором. Причина: ' . $informs->dop;
        if ($informs->save()) {
            return $informs;
        }

        return null;
    }

    /**
     * @param Contracts $contract
     *
     * @return Informs|null
     */
    public static function CreateFoContractRefuseByOrganization(Contracts $contract)
    {
        $informs = self::build($contract, '', UserIdentity::ROLE_ORGANIZATION_ID, $contract->organization_id);
        if (!$informs->load(Yii::$app->request->post())) {
            return null;
        }
        $informs->text = 'Заявка отменена поставщиком услуг. Причина: ' . $informs->dop;
        if ($informs->save()) {
            return $informs;
        }

        return null;
    }
}