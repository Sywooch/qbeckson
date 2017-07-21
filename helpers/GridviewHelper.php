<?php

namespace app\helpers;

use Yii;

/**
 * Class GridviewHelper
 * @package app\helpers
 */
class GridviewHelper
{
    /**
     * @param $table
     * @param $columns
     * @param string $excludeType
     * @param array|null $excludeAttributes
     * @return mixed
     */
    public static function prepareColumns(
        $table,
        array $columns,
        $excludeType = 'gridView',
        $excludeAttributes = ['type', 'data', 'searchFilter', 'gridView']
    ) {
        if ($userFilter = Yii::$app->user->identity->getFilterSettings($table)) {
            $inaccessibleColumns = $userFilter->filter->inaccessibleColumns;
            $otherColumns = $userFilter->columns;
        }

        foreach ($columns as $index => $column) {
            if (isset($column[$excludeType]) && $column[$excludeType] !== true) {
                unset($columns[$index]);
                continue;
            }

            if (!empty($userFilter) && isset($column['attribute'])) {
                if (!in_array($column['attribute'], $inaccessibleColumns) && !in_array($column['attribute'], $otherColumns)) {
                    unset($columns[$index]);
                    continue;
                }
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
