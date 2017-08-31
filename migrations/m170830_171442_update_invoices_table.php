<?php

use yii\db\Migration;

class m170830_171442_update_invoices_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('invoices', 'cooperate_id', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('invoices', 'cooperate_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170830_171442_update_invoices_table cannot be reverted.\n";

        return false;
    }
    */
}
