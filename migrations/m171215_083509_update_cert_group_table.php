<?php

use yii\db\Migration;

class m171215_083509_update_cert_group_table extends Migration
{
    public function safeUp()
    {
        $command = $this->db->createCommand("UPDATE `cert_group` SET nominals_limit = CEIL(amount * nominal) WHERE 1");

        $command->execute();
    }

    public function safeDown()
    {
        $command = $this->db->createCommand("UPDATE `cert_group` SET nominals_limit = 0 WHERE 1");

        $command->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171215_083509_update_cert_group_table cannot be reverted.\n";

        return false;
    }
    */
}
