<?php

namespace app\models;

/*
 * Генерирует главное меню для разных ролей
 * */
class Menu
{
    public static function getByCurrentUser(): array
    {
        $webUser = \Yii::$app->user;
        if ($webUser->isGuest) {
            return self::getFoGuest();
        } elseif ($webUser->can(UserIdentity::ROLE_ADMINISTRATOR)) {
            return self::getFoAdmin();
        } elseif ($webUser->can(UserIdentity::ROLE_OPERATOR)) {
            return self::getFoOperator();
        } elseif ($webUser->can(UserIdentity::ROLE_PAYER)) {
            return self::getFoPayer();
        } elseif ($webUser->can(UserIdentity::ROLE_ORGANIZATION)) {
            return self::getFoOrganisation();
        } elseif ($webUser->can(UserIdentity::ROLE_CERTIFICATE)) {
            return self::getFoCertificate();
        }

        return null;
    }


    public static function getFoGuest(): array
    {
        return [];
    }

    public static function getFoAdmin(): array
    {
        return [
            ['label' => 'Главная', 'url' => ['/site/index']],
            ['label' => 'Справочный раздел', 'url' => ['/site/about']],
            ['label' => 'Обратная связь', 'url' => ['/site/contact']],
            ['label' => 'Поиск программ', 'url' => ['/programs/index']],
        ];
    }

    public static function getFoOperator(): array
    {
        return [
            ['label' => 'Информация', 'items' => [
                ['label' => 'Статическая информация', 'url' => ['/personal/operator-statistic']],
                ['label' => 'Сведения об операторе', 'url' => ['/personal/operator-info']],
            ]],
            ['label' => 'Коэффициенты', 'items' => [
                ['label' => 'Муниципалитеты', 'url' => ['/mun/index']],
                ['label' => 'Общие параметры', 'url' => ['/coefficient/update']],
            ]],
            ['label' => 'Плательщики', 'url' => ['/personal/operator-payers']],
            ['label' => 'Организации', 'url' => ['/personal/operator-organizations']],
            ['label' => 'Сертификаты', 'url' => ['/personal/operator-certificates']],
            ['label' => 'Договоры', 'url' => ['/personal/operator-contracts']],
            ['label' => 'Программы', 'url' => ['/personal/operator-programs']],
        ];
    }

    public static function getFoPayer(): array
    {
        return [
            ['label' => 'Информация', 'items' => [
                ['label' => 'Статическая информация', 'url' => ['/personal/payer-statistic']],
                ['label' => 'Сведения о плательщике', 'url' => ['/personal/payer-info']],
            ]],
            ['label' => 'Стоимость групп', 'url' => ['/cert-group/index']],
            ['label' => 'Сертификаты', 'url' => ['/personal/payer-certificates']],
            ['label' => 'Договоры', 'url' => ['/personal/payer-contracts']],
            ['label' => 'Счета', 'url' => ['/personal/payer-invoices']],
            ['label' => 'Организации', 'url' => ['/personal/payer-organizations']],
            ['label' => 'Программы', 'url' => ['/personal/payer-programs']],
        ];
    }

    public static function getFoOrganisation(): array
    {
        return [
            ['label' => 'Информация', 'items' => [
                ['label' => 'Статическая информация', 'url' => ['/personal/organization-statistic']],
                ['label' => 'Сведения об организации', 'url' => ['/personal/organization-info']],
                ['label' => 'Предварительные записи', 'url' => ['/personal/organization-favorites']],
            ]],
            ['label' => 'Программы', 'url' => ['/personal/organization-programs']],
            ['label' => 'Договоры', 'url' => ['/personal/organization-contracts']],
            ['label' => 'Счета', 'url' => ['/personal/organization-invoices']],
            ['label' => 'Плательщики', 'url' => ['/personal/organization-payers']],
            ['label' => 'Группы', 'url' => ['/personal/organization-groups']],
        ];
    }

    public static function getFoCertificate(): array
    {
        return [
            ['label' => 'Информация', 'url' => ['/personal/certificate-statistic']],
            ['label' => 'Программы', 'items' => [
                ['label' => 'Обучение в текущем году', 'url' => ['/personal/certificate-programs']],
                ['label' => 'Предварительная запись', 'url' => ['/personal/certificate-previus']],
            ]],
            ['label' => 'Договоры', 'url' => ['/personal/certificate-contracts']],
            ['label' => 'Избранное', 'url' => ['/personal/certificate-favorites']],
        ];
    }
}