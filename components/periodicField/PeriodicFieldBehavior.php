<?php


namespace app\components\periodicField;


use yii\base\Behavior;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;
use yii\db\Transaction;

/**
 * Class PeriodicFieldBehavior
 * @package app\components\periodicField
 * @property Transaction $transaction
 */
class PeriodicFieldBehavior extends Behavior
{
    public $blackListFields = [
        'id', 'created_at', 'updated_at', 'created_by', 'updated_by'
    ];

    public $whiteListFields = null;

    private $transaction;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beginTransaction',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beginTransaction',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave'
        ];
    }


    public function beginTransaction($event)
    {
        $this->transaction = \Yii::$app->db->beginTransaction();
    }

    public function afterSave(AfterSaveEvent $event)
    {
        /**@var $sender ActiveRecord */
        $sender = $event->sender;
        $table = $sender::tableName();
        $fields = $event->changedAttributes;
        $now = time();
        foreach ($fields as $field => $oldValue) {
            if (in_array($field, $this->blackListFields)) {
                continue;
            }
            if ($this->whiteListFields && !in_array($field, $this->whiteListFields)) {
                continue;
            }
            if ($oldValue == $sender->{$field}) {
                continue;
            }
            $historyRecord = new PeriodicFieldAR();
            $historyRecord->table_name = $table;
            $historyRecord->field_name = $field;
            $historyRecord->record_id = $sender->getPrimaryKey();
            $historyRecord->value = (string)$sender->{$field};
            $historyRecord->created_at = $now;
            if (!$historyRecord->save()) {
                \Yii::trace($historyRecord->getErrors());
                $this->transaction->rollback();
                throw new Exception('Не удалось сохранить историю поля' . $historyRecord->field_name);
            }
        }
        $this->transaction->commit();
    }
}
