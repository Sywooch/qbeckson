<?php

namespace app\helpers;


class DeclinationOfMonths
{
    const  PREPOSITIONAL = 'prepositional';
    const  NOMINATIVE = 'nominative';


    public static function getMonthNameByNumber(int $number, $case)
    {
        if ($case === self::PREPOSITIONAL) {
            return self::getMonthNameByNumberAsPrepositional($number);
        } elseif ($case === self::NOMINATIVE) {
            return self::getMonthNameByNumberAsNominative($number);
        }
    }

    public static function getMonthNameByNumberAsPrepositional(int $number)
    {
        switch ($number) {
            case 1:
                $m = 'январе';
                break;
            case 2:
                $m = 'феврале';
                break;
            case 3:
                $m = 'марте';
                break;
            case 4:
                $m = 'апреле';
                break;
            case 5:
                $m = 'мае';
                break;
            case 6:
                $m = 'июне';
                break;
            case 7:
                $m = 'июле';
                break;
            case 8:
                $m = 'августе';
                break;
            case 9:
                $m = 'сентябре';
                break;
            case 10:
                $m = 'октябре';
                break;
            case 11:
                $m = 'ноябре';
                break;
            case 12:
                $m = 'декабре';
                break;
            default:
                $m = null;
        }

        return $m;
    }

    public static function getMonthNameByNumberAsNominative(int $number)
    {
        switch ($number) {
            case 1:
                $m = 'январь';
                break;
            case 2:
                $m = 'февраль';
                break;
            case 3:
                $m = 'март';
                break;
            case 4:
                $m = 'апрель';
                break;
            case 5:
                $m = 'май';
                break;
            case 6:
                $m = 'июнь';
                break;
            case 7:
                $m = 'июль';
                break;
            case 8:
                $m = 'август';
                break;
            case 9:
                $m = 'сентябрь';
                break;
            case 10:
                $m = 'октябрь';
                break;
            case 11:
                $m = 'ноябрь';
                break;
            case 12:
                $m = 'декабрь';
                break;
            default:
                $m = null;
        }

        return $m;
    }

}
