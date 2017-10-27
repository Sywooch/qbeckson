<?php

use yii\db\Migration;

class m171023_051420_alter_help_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('help', 'order_id', $this->integer(11)->defaultValue(null)->comment('идентификатор для сортировки'));
    }

    public function safeDown()
    {
        $this->dropColumn('help', 'order_id');
    }
}
