<?php

use yii\db\Migration;

class m170629_111900_update_mun_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('mun', 'operator_id', $this->integer()->null() . ' AFTER `id`');
        $this->addForeignKey('fk-mun-operator', 'mun', 'operator_id', 'operators', 'id', 'cascade', 'cascade');

    }

    public function safeDown()
    {
        $this->dropColumn('mun', 'operator_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170629_111900_update_mun_table cannot be reverted.\n";

        return false;
    }
    */
}
