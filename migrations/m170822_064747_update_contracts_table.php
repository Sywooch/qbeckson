<?php

use yii\db\Migration;

class m170822_064747_update_contracts_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('contracts', 'date_initiate_termination', $this->date());
    }

    public function safeDown()
    {
        $this->dropColumn('contracts', 'date_initiate_termination');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170822_064747_update_contracts_table cannot be reverted.\n";

        return false;
    }
    */
}
