<?php

use yii\db\Migration;

class m170515_111105_update_organization_table extends Migration
{
    public function up()
    {
        $this->alterColumn('organization', 'organizational_form', $this->integer());
    }

    public function down()
    {
        echo "m170515_111105_update_organization_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
