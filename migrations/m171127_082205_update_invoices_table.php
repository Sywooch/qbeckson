<?php

use yii\db\Migration;

class m171127_082205_update_invoices_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('invoices', 'year', $this->integer()->after('month'));
        $this->update('invoices', ['year' => 2017]);
    }

    public function safeDown()
    {
        $this->dropColumn('invoices', 'year');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171127_082205_update_invoices_table cannot be reverted.\n";

        return false;
    }
    */
}
