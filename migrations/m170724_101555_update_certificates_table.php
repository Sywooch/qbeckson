<?php

use yii\db\Migration;

class m170724_101555_update_certificates_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('certificates', 'possible_cert_group', $this->integer()->defaultValue(0));
        $this->db->createCommand('UPDATE `certificates` SET possible_cert_group = cert_group')->execute();
    }

    public function safeDown()
    {
        $this->dropColumn('certificates', 'possible_cert_group');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170724_101555_update_certificates_table cannot be reverted.\n";

        return false;
    }
    */
}
