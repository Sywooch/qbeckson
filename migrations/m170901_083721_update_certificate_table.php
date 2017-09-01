<?php

use yii\db\Migration;

class m170901_083721_update_certificate_table extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('certificates', 'nominal', $this->float());
        $this->alterColumn('certificates', 'balance', $this->float());
    }

    public function safeDown()
    {
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170901_083721_update_certificate_table cannot be reverted.\n";

        return false;
    }
    */
}
