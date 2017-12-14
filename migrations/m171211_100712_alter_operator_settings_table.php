<?php

use yii\db\Migration;

class m171211_100712_alter_operator_settings_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('operator_settings', 'day_offset', $this->smallInteger()->notNull()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('operator_settings', 'day_offset');
    }
}
