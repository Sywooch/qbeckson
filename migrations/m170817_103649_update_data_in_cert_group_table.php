<?php

use yii\db\Migration;

class m170817_103649_update_data_in_cert_group_table extends Migration
{
    public function safeUp()
    {
        $this->execute('UPDATE `cert_group` SET `nominal_f` = `nominal`');
        $this->alterColumn('cert_group', 'nominal', 'DECIMAL(19,2) NOT NULL DEFAULT 0');
    }

    public function safeDown()
    {
        $this->dropColumn('cert_group', 'nominal_p'); /*todo что то не так*/
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170817_103649_update_data_in_cert_group_table cannot be reverted.\n";

        return false;
    }
    */
}
