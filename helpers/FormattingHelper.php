<?php

namespace app\helpers;

use Yii;
use Arius\NumberFormatter;

/**
 * Class FormattingHelper
 * @package app\helpers
 */
class FormattingHelper
{
	public static function asSpelloutOrdinal($value)
	{
	    $formatter = new NumberFormatter('ru', NumberFormatter::SPELLOUT);
        $formatter->setTextAttribute(NumberFormatter::DEFAULT_RULESET, "%spellout-ordinal");

        return $formatter->format($value);
	}

    public static function directivityForm($value)
    {
        $directivity = '';

        if ($value == 'Техническая (робототехника)' or $value == 'Техническая (иная)') {
            $directivity = 'технической';
        }
        if ($value == 'Естественнонаучная') {
            $directivity = 'естественнонаучной';
        }
        if ($value == 'Физкультурно-спортивная') {
            $directivity = 'физкультурно-спортивной';
        }
        if ($value == 'Художественная') {
            $directivity = 'художественной';
        }
        if ($value == 'Туристско-краеведческая') {
            $directivity = 'туристско-краеведческой';
        }
        if ($value == 'Социально-педагогическая') {
            $directivity = 'социально-педагогической';
        }

        return $directivity;
    }
}
