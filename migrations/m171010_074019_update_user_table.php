<?php

use yii\db\Migration;

class m171010_074019_update_user_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('user', 'status', $this->integer()->defaultValue(10));
        $this->addColumn('user', 'block_reason', $this->text());
    }

    public function safeDown()
    {
        $this->dropColumn('user', 'status');
        $this->dropColumn('user', 'block_reason');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171010_074019_update_user_table cannot be reverted.\n";

        return false;
    }
    */
}
