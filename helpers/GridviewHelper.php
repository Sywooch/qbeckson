<?php

namespace app\helpers;

use app\models\UserIdentity;
use Yii;

/**
 * Class GridviewHelper
 * @package app\helpers
 */
class GridviewHelper
{
    /**
     * @param $table
     * @param array $columns
     * @param null|string $type
     * @param string $excludeType
     * @param array|null $excludeAttributes
     * @return mixed
     */
    public static function prepareColumns(
        $table,
        array $columns,
        $type = null,
        $excludeType = 'gridView',
        $excludeAttributes = ['type', 'data', 'searchFilter', 'gridView']
    ) {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->identity;
        if ($userFilter = $user->getFilterSettings($table, $type)) {
            $inaccessibleColumns = $userFilter->filter->inaccessibleColumns;
            $otherColumns = $userFilter->columns;
        }

        /*if (null === $userFilter) {
            throw new \DomainException('Something wrong');
        }*/

        foreach ($columns as $index => $column) {
            if (isset($column[$excludeType]) && $column[$excludeType] !== true) {
                unset($columns[$index]);
                continue;
            }

            if (!empty($userFilter) &&
                isset($column['attribute']) &&
                !in_array($column['attribute'], $inaccessibleColumns, true) &&
                !in_array($column['attribute'], $otherColumns, true)
            ) {
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
