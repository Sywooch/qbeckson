<?php

namespace app\helpers;

use Yii;
use app\models\Organization;

/**
 * Class PermissionHelper
 * @package app\helpers
 */
class PermissionHelper
{
	public static function redirectUrlByRole()
	{
        $url = ['site/error'];
        if (Yii::$app->user->can('admins')) {
            $url = ['/personal/index'];
        } elseif (Yii::$app->user->can('operators')) {
            $url = ['/personal/operator-statistic'];
        } elseif (Yii::$app->user->can('payer')) {
            $url = ['/personal/payer-statistic'];
        } elseif (Yii::$app->user->can('organizations')) {
            $url = ['/personal/organization-statistic'];

            $organization = Yii::$app->user->identity->organization;

            if ($organization->type != Organization::TYPE_IP_WITHOUT_WORKERS) {
                if (empty($organization['license_issued_dat']) || empty($organization['fio']) || empty($organization['position']) || empty($organization['doc_type'])) {
                    Yii::$app->session->setFlash('warning', 'Заполните информацию "Для договора"');

                    $url = ['/personal/organization-info'];
                }

                // TODO: Разобраться че за doc_type, заменить на константы
                if ($organization->doc_type == 1) {
                    if (empty($organization['date_proxy']) || empty($organization['number_proxy'])) {
                        Yii::$app->session->setFlash('warning', 'Заполните информацию "Для договора"');
                        $url = ['/personal/organization-info'];
                    }
                 }
            }
        } elseif (Yii::$app->user->can('certificate')) {
            $url = ['/personal/certificate-statistic'];
        }

        return $url;
	}
}
