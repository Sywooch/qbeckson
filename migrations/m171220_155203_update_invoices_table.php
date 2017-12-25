<?php

use yii\db\Migration;

class m171220_155203_update_invoices_table extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('invoices', 'number', $this->string(32));
    }

    public function safeDown()
    {
        $this->alterColumn('invoices', 'number', $this->integer());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171220_155203_update_invoices_table cannot be reverted.\n";

        return false;
    }
    */
}
