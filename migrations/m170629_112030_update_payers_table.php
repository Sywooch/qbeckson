<?php

use yii\db\Migration;

class m170629_112030_update_payers_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('payers', 'operator_id', $this->integer()->null() . ' AFTER `user_id`');
        $this->addForeignKey('fk-payers-operator', 'payers', 'operator_id', 'operators', 'id', 'cascade', 'cascade');

    }

    public function safeDown()
    {
        $this->dropColumn('payers', 'operator_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170629_112030_update_payers_table cannot be reverted.\n";

        return false;
    }
    */
}
