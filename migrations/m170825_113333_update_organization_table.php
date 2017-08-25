<?php

use yii\db\Migration;

class m170825_113333_update_organization_table extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('organization', 'KPP', $this->integer()->defaultValue(0));
        $this->addColumn('organization', 'receiver', $this->string()->null());
    }

    public function safeDown()
    {
        $this->dropColumn('organization', 'receiver');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170825_113333_update_organization_table cannot be reverted.\n";

        return false;
    }
    */
}
