<?php

use yii\db\Migration;

class m170629_104254_update_coefficient_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('coefficient', 'operator_id', $this->integer()->null() . ' AFTER `id`');
        $this->addForeignKey('fk-coefficient-operator', 'coefficient', 'operator_id', 'operators', 'id', 'cascade', 'cascade');
    }

    public function safeDown()
    {
        $this->dropColumn('coefficient', 'operator_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170629_104254_update_coefficient_table cannot be reverted.\n";

        return false;
    }
    */
}
