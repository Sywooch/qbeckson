<?php

namespace app\helpers;

use app\models\UserIdentity;
use app\widgets\SearchFilter;
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
        $excludeAttributes = ['type', 'data', 'searchFilter', 'gridView', 'pluginOptions', 'export'],
        $customizable = true
    ) {
        /** @var UserIdentity $user */
        $user = Yii::$app->user->identity;
        if ($customizable && ($userFilter = $user->getFilterSettings($table, $type))) {
            $inaccessibleColumns = $userFilter->filter->inaccessibleColumns;
            $otherColumns = $userFilter->columns;
        } else {
            $userFilter = null;
        }
        foreach ($columns as $index => $column) {
            if (isset($column['type']) && $column['type'] === SearchFilter::TYPE_HIDDEN && null !== $excludeAttributes) {
                unset($columns[$index]);
                continue;
            }

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

    /**
     * @deprecated
     * @param array $columns
     * @param array $excludeAttributes
     * @return array
     */
    public static function prepareExportColumns(
        array $columns,
        array $excludeAttributes = ['type', 'data', 'searchFilter', 'gridView', 'pluginOptions']
    ) {
        foreach ($columns as $index => $column) {
            if (isset($column['type']) && $column['type'] === SearchFilter::TYPE_HIDDEN) {
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
        array_pop($columns);

        return $columns;
    }

    public static function getFileName($type, $datetime = false)
    {
        //$str = Yii::$app->security->generateRandomString(12) . '---' . $type;
        // TODO: вернуть рандомное название с перезаписыванием выписки
        $str = Yii::$app->user->id . '---' . $type;

        if ($datetime === true) {
            $str .=  '-' . date('d-m-Y-H-i');
        }

        return $str;
    }
}
