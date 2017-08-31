<?php

use yii\db\Migration;

class m170830_171349_update_contracts_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('contracts', 'cooperate_id', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('contracts', 'cooperate_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170830_171349_update_contracts_table cannot be reverted.\n";

        return false;
    }
    */
}
