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
}
