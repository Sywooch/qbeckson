<?php

namespace app\components\periodicField;


use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

trait PeriodicField
{
    /**
     * @param $field string
     *
     * @return ActiveQuery
     *
     * @throws Exception
     */
    public function getHistoryQuery(string $field = null)
    {
        /** @var $self ActiveRecord */
        $self = $this;
        if ($field && !in_array($field, $self->fields())) {
            throw new Exception('Не известное поле ' . $field);
        }
        $table = $self::tableName();
        $record_id = $self->getPrimaryKey();

        return PeriodicFieldAR::find()
            ->where(
                [
                    'table_name' => $table,
                    'record_id' => $record_id,

                ]
            )->andFilterWhere(['field_name' => $field]);
    }

    public function getHistory(string $field)
    {
        return $this
            ->getHistoryQuery($field)
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
    }

    public function getLastValue(string $field)
    {
        $result = $this
            ->getHistoryQuery($field)
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(1)
            ->one();

        return $result ? $result->value : null;
    }
}
