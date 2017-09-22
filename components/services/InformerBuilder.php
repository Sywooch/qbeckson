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
        $informs = new Informs();
        if ($informs->load(Yii::$app->request->post())) {
            return null;
        }
        $informs->program_id = $contract->program_id;
        $informs->contract_id = $contract->id;
        $informs->prof_id = $contract->certificate_id;
        $informs->text = 'Договор расторжен клиентом. Причина: ' . $informs->dop;
        $informs->from = UserIdentity::ROLE_CERTIFICATE_ID;
        $informs->date = date("Y-m-d");
        $informs->read = 0;
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
        $informs = new Informs();
        if ($informs->load(Yii::$app->request->post())) {
            return null;
        }
        $informs->program_id = $contract->program_id;
        $informs->contract_id = $contract->id;
        $informs->prof_id = $contract->certificate_id;
        $informs->text = 'Договор расторжен поставщиком услуг. Причина: ' . $informs->dop;
        $informs->from = UserIdentity::ROLE_CERTIFICATE_ID;
        $informs->date = date("Y-m-d");
        $informs->read = 0;
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
        $informs = new Informs();
        if ($informs->load(Yii::$app->request->post())) {
            return null;
        }
        $informs->program_id = $contract->program_id;
        $informs->contract_id = $contract->id;
        $informs->prof_id = $contract->certificate_id;
        $informs->text = 'Заявка отменена клиентом. Причина: ' . $informs->dop;
        $informs->from = UserIdentity::ROLE_CERTIFICATE_ID;
        $informs->date = date("Y-m-d");
        $informs->read = 0;
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
        $informs = new Informs();
        if ($informs->load(Yii::$app->request->post())) {
            return null;
        }
        $informs->program_id = $contract->program_id;
        $informs->contract_id = $contract->id;
        $informs->prof_id = $contract->organization_id;
        $informs->text = 'Заявка отменена поставщиком услуг. Причина: ' . $informs->dop;
        $informs->from = UserIdentity::ROLE_ORGANIZATION_ID;
        $informs->date = date("Y-m-d");
        $informs->read = 0;
        if ($informs->save()) {

            return $informs;
        }

        return null;
    }
}