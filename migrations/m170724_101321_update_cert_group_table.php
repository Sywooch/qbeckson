<?php

use yii\db\Migration;

class m170724_101321_update_cert_group_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('cert_group', 'amount', $this->integer()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('cert_group', 'amount');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170724_101321_update_cert_group_table cannot be reverted.\n";

        return false;
    }
    */
}
