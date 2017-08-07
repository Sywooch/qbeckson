<?php

use yii\db\Migration;

class m170807_130920_update_certificate_group_queue_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('certificate_group_queue', 'created_at', $this->integer()->notNull());
    }

    public function safeDown()
    {
        $this->dropColumn('certificate_group_queue', 'created_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170807_130920_update_certificate_group_queue_table cannot be reverted.\n";

        return false;
    }
    */
}
