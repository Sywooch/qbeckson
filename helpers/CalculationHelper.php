<?php

namespace app\helpers;

/**
 * Class CalculationHelper
 * @package app\helpers
 */
class CalculationHelper
{
    const TO_UP = 'up';
    const TO_DOWN = 'down';

    /**
     * Округление до сотых в большую или меньшую сторону
     *
     * @param float $value
     * @param string $method
     * @return float|int
     */
    public static function roundTo($value, $method = 'up')
    {
        $valueArray = explode('.', $value * 1000);
        $result = substr(array_shift($valueArray), 0, -1) / 100;

        return $method === self::TO_UP ? static::roundUp($value) : $result;
    }

    public static function roundUp($value, $precision = 2)
    {
        $pow = pow(10, $precision);

        return (ceil($pow * $value) + ceil($pow * $value - ceil($pow * $value))) / $pow;
    }

    /**
     * @param string $dateFrom date in format
     * @param string $dateTo date in format
     * @return integer
     */
    public static function daysBetweenDates($dateFrom, $dateTo)
    {
        $datetime1 = new \DateTime($dateFrom);
        $datetime2 = new \DateTime($dateTo);
        $interval = $datetime1->diff($datetime2);

        return (int)($interval->days + 1);
    }

    /**
     * @param string $dateFrom date in format
     * @param string $dateTo date in format
     * @return integer
     */
    public static function monthesInPeriod($dateFrom, $dateTo)
    {
        $datetime1 = new \DateTime(date('Y-m', strtotime($dateFrom)));
        $datetime2 = new \DateTime(date('Y-m', strtotime($dateTo)));
        $interval = $datetime1->diff($datetime2);

        return (int)(round(($interval->m * 30 + $interval->d) / 30) + 1);
    }
}
