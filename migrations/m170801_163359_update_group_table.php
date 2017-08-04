<?php

use yii\db\Migration;

class m170801_163359_update_group_table extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('groups', 'address', 'TEXT DEFAULT NULL');
        $this->alterColumn('groups', 'schedule', 'TEXT DEFAULT NULL');
    }

    public function safeDown()
    {
        return true;
    }
}
