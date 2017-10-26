<?php

namespace app\models;

/**
 * Class to set order_id`s of Help objects
 */
class HelpOrdering
{
    /**
     * Sets order_id`s from 1, if exists repetitive order_id
     */
    public static function setOrderIds()
    {
        static $helpList = null;

        if (is_null($helpList)) {
            $helpList = Help::find()->orderBy('order_id')->all();
        }

        $orderIdList = [];
        foreach ($helpList as $help) {
            if ($help->order_id && !in_array($help->order_id, $orderIdList)) {
                $orderIdList[] = $help->order_id;
            }
        }

        if (count($orderIdList) < count($helpList)) {
            $orderIndex = 1;
            foreach ($helpList as $help) {
                $help->order_id = $orderIndex;
                $help->update();
                $orderIndex++;
            }
        }
    }

    /**
     * Change order_id
     * ---
     * if $increaseOrder is true, increases order, else reduces
     * @param $id
     * @param $increaseOrder
     */
    public static function changeOrder($id, $increaseOrder)
    {
        self::setOrderIds();

        $help = Help::findOne($id);

        if ($increaseOrder && !$help->isOrderMax()) {
            $helpWithHigherOrder = Help::findOne(['order_id' => $help->order_id + 1]);
            $helpWithHigherOrder->order_id = $help->order_id;
            $help->order_id += 1;

            $helpWithHigherOrder->update();
            $help->update();
        }

        if (!$increaseOrder && !$help->isOrderMin()) {
            $helpWithLowerOrder = Help::findOne(['order_id' => $help->order_id - 1]);
            $helpWithLowerOrder->order_id = $help->order_id;
            $help->order_id -= 1;

            $helpWithLowerOrder->update();
            $help->update();
        }
    }
}