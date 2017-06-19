<?php

namespace app\helpers;

use Yii;

/**
 * Class GridviewHelper
 * @package app\helpers
 */
class GridviewHelper
{
    public static function prepareColumns($columns, $excludeType = 'gridView', $excludeAttributes = ['type', 'data', 'searchFilter', 'gridView']) {
        foreach ($columns as $index => $column) {
            if (isset($column[$excludeType]) && $column[$excludeType] !== true) {
                unset($columns[$index]);
                continue;
            }

            if (!empty($excludeAttributes)) {
                foreach ($excludeAttributes as $excludeAttribute) {
                    if (isset($column[$excludeAttribute])) {
                        unset($columns[$index][$excludeAttribute]);
                    }
                }
            }
        }

        return $columns;
    }
}
