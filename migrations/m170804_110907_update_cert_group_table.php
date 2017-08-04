<?php

use yii\db\Migration;

class m170804_110907_update_cert_group_table extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('cert_group', 'nominal', $this->float());
    }

    public function safeDown()
    {
        $this->alterColumn('cert_group', 'nominal', $this->integer());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170804_110907_update_cert_group_table cannot be reverted.\n";

        return false;
    }
    */
}
