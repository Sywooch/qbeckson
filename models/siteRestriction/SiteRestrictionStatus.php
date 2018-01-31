<?php

namespace app\models\siteRestriction;

/**
 * статус запрета доступа к сайту
 */
class SiteRestrictionStatus
{
    /**
     * запрет неактивен
     */
    const NOT_ACTIVE = 0;

    /**
     * запрет активен
     */
    const ACTIVE = 1;

    /**
     * получить список статусов запрета доступа к сайту
     *
     * @return string[]
     */
    public static function getList()
    {
        return [
            self::NOT_ACTIVE => self::NOT_ACTIVE,
            self::ACTIVE => self::ACTIVE,
        ];
    }

    /**
     * получить список названий статусов запрета доступа к сайту
     *
     * @return string[]
     */
    public static function getLabelList()
    {
        return [
            self::NOT_ACTIVE => 'Неактивен',
            self::ACTIVE => 'Активен',
        ];
    }

    /**
     * получить название статуса запрета доступа к сайту
     *
     * @param string $status
     *
     * @return null|string
     */
    public static function getLabel($status)
    {
        return array_key_exists($status, self::getLabelList()) ? self::getLabelList()[$status] : null;
    }
}