<?php

use yii\db\Migration;

class m171206_143836_alter_cooperate_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('cooperate', 'period', $this->boolean()->defaultValue(1)->comment('период действия соглашения, 0 - архивный, 1 - текущий, 2 - будущий'));
    }

    public function safeDown()
    {
        $this->dropColumn('cooperate', 'period');
    }
}
