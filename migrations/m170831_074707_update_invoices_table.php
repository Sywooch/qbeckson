<?php

use yii\db\Migration;

class m170831_074707_update_invoices_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('invoices', 'pdf', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('invoices', 'pdf');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170831_074707_update_invoices_table cannot be reverted.\n";

        return false;
    }
    */
}
