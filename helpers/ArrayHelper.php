<?php

namespace app\helpers;

/**
 * Class ArrayHelper
 * @package app\helpers
 */
class ArrayHelper extends \yii\helpers\ArrayHelper
{
	public static function removeItemByValue($array, $value)
	{
        $key = array_search($value, $array);
        if ($key !== false) {
            unset($array[$key]);
        }

        return $array;
	}

	public static function divide($array, $size, $num = null)
	{
        $arrayOutput = array_chunk($array, $size);
        if ($num) {
            array_splice($arrayOutput, $num);
        }

	    return $arrayOutput;
	}

    public static function groupByValue($parentArray, $valueName)
    {
        $groupedArray = [];

        foreach ($parentArray as $key => $value)
        {
            if (!is_array($value)) {
                $convertedValue = static::toArray($value);
            } else {
                $convertedValue = $value;
            }
            
            if (array_key_exists($valueName, $convertedValue)) {
                $groupedArray[$convertedValue[$valueName]][$key] = $value;
            }
        }

        return $groupedArray;
    }
}
