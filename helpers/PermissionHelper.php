<?php

namespace app\helpers;

use Yii;
use app\models\Organization;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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

    public static function getMenuItems()
    {
        $items = [
            ['label' => 'Информация', 'items' => [
                ['label' => 'Общая статистика', 'url' => ['/personal/payer-statistic']],
                ['label' => 'Уполномоченные организации', 'url' => ['/monitor/index']],
                ['label' => 'Инструкции по работе в личном кабинете', 'url' => ['site/manuals']],
                ['label' => 'Объединение кабинетов', 'url' => ['/personal/user-personal-assign']],
                ['label' => 'Муниципальные параметры', 'url' => [
                    '/mun/view', 'id' => ArrayHelper::getValue(Yii::$app->user->identity, ['payer', 'mun'])
                ]],
            ]],
            ['label' => 'Номиналы групп', 'url' => ['/cert-group/index']],
            ['label' => 'Сертификаты', 'url' => ['/personal/payer-certificates']],
            ['label' => 'Договоры', 'url' => ['/personal/payer-contracts']],
            ['label' => 'Счета', 'url' => ['/personal/payer-invoices']],
            ['label' => 'Организации', 'items' => [
                ['label' => 'Реестр ПФДО', 'url' => ['/personal/payer-organizations']],
                ['label' => 'Подведомственные организации', 'url' => ['/personal/payer-suborder-organizations']],
            ]],
            ['label' => 'Программы', 'items' => [
                [
                    'label' => 'Реестр программ',
                    'url' => ['personal/payer-programs']
                ],
                [
                    'label' => 'Программы по муниципальному заданию',
                    'url' => ['personal/payer-municipal-task']
                ],
            ]],
        ];

        return static::checkMenuAccess($items);
    }

    public static function checkMenuAccess($items)
    {
        foreach ($items as $index => $item) {
            if (isset($item['items'])) {
                $items[$index]['items'] = static::checkMenuAccess($item['items']);
                continue;
            }

            if (!static::checkMonitorUrl($item['url'][0])) {
                $items[$index]['visible'] = false;
            }
        }

        return $items;
    }

    public static function getAccessUrls()
    {
        $items = [
            ['index' => 'Общая статистика', 'value' => '/personal/payer-statistic'],
            ['index' => 'Наблюдатели', 'value' => '/monitor/index'],
            ['index' => 'Номиналы групп', 'value' => '/cert-group/index'],
            ['index' => 'Сертификаты', 'value' => '/personal/payer-certificates'],
            ['index' => 'Договоры', 'value' => 'contracts'],
            ['index' => 'Счета', 'value' => 'invoices'],
            ['index' => 'Организации', 'value' => 'organization'],
            ['index' => 'Работа с договором между плательщиками и поставщиками', 'value' => 'cooperate'],
            ['index' => 'Программы', 'value' => 'programs'],
            ['index' => 'Муниципальные задания', 'value' => 'municipal-task'],
            ['index' => 'Параметры муниципальных заданий', 'value' => 'matrix'],
            ['index' => 'Создание сертификата', 'value' => '/certificates/create'],
            ['index' => 'Просмотр сертификата', 'value' => '/certificates/view'],
            ['index' => 'Редактирование сертификата', 'value' => '/certificates/update'],
            ['index' => 'Удаление сертификата', 'value' => '/certificates/delete'],
            ['index' => 'Активация / заморозка сертификата', 'value' => '/certificates/actual'],
            ['index' => 'Обновление номиналов', 'value' => '/certificates/allnominal'],
        ];

        return ArrayHelper::map($items, 'value', 'index');
    }

    public static function checkMonitorUrl($requestUrl = null)
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->isMonitored) {
            return true;
        }

        $urls = Yii::$app->user->identity->monitor->userMonitorAssignment->access_rights;
        $currentUrl = empty($requestUrl) ? Url::toRoute(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH)) : $requestUrl;

        if ($currentUrl == '/' || strpos($currentUrl, 'site/') || strpos($currentUrl, 'debug/')) {
            return true;
        }

        $matches = array_filter($urls, function ($url) use ($currentUrl) {
            return (strpos($currentUrl, $url) !== false) ? true : false;
        });

        if (!empty($matches)) {
            return true;
        }

        return false;
    }
}
