<?php

namespace app\models\siteRestriction;

/**
 * тип запрета доступа к сайту
 */
class SiteRestrictionType
{
    /**
     * запрет действует всегда пока активен
     */
    const ALWAYS = 'always';

    /**
     * запрет действует во время работы крона
     */
    const CRON = 'cron';

    /**
     * получить список возможных типов запрета доступа к сайту
     */
    public static function getList()
    {
        return [
            self::ALWAYS => self::ALWAYS,
            self::CRON => self::CRON,
        ];
    }

    /**
     * получить список типов запрета с названиями
     *
     * @return array
     */
    public static function getLabelList()
    {
        $type_label_list = [
            self::ALWAYS => 'Всегда пока активен',
            self::CRON => 'Во время работы крона',
        ];

        return $type_label_list;
    }

    /**
     * получить название типа запрета
     *
     * @param string $type
     *
     * @return string|null
     */
    public static function getLabel($type)
    {
        return array_key_exists($type, self::getLabelList()) ? self::getLabelList()[$type] : null;
    }
}