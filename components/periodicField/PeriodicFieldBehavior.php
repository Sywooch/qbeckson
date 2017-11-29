<?php


namespace app\components\periodicField;


use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;

class PeriodicFieldBehavior extends Behavior
{
    public $blackListFields = [
        'id', 'created_at', 'updated_at', 'created_by', 'updated_by'
    ];

    public $whiteListFields = null;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave'
        ];
    }


    public function afterSave(AfterSaveEvent $event)
    {
        /**@var $sender ActiveRecord */
        $sender = $event->sender;
        $table = $sender::tableName();
        $fields = $event->changedAttributes;
        foreach ($fields as $field) {
            if (in_array($field, $this->blackListFields)) {
                continue;
            }
            if ($this->whiteListFields && !in_array($field, $this->whiteListFields)) {
                continue;
            }
            $historyRecord = new PeriodicFieldAR();
            //$historyRecord->

        }
    }
}
